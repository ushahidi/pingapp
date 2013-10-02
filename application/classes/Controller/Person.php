<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Person Controller
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Controller_Person extends Controller_PingApp {
	
	/**
	 * Add/Edit Person
	 * 
	 * @return void
	 */
	public function action_edit()
	{
		$this->template->content = View::factory('pages/person/edit')
			->bind('user', $this->user)
			->bind('groups', $groups)
			->bind('person', $person)
			->bind('parent', $parent)
			->bind('post', $post)
			->bind('errors', $errors)
			->bind('done', $done);

		$groups = $this->user->groups
			->order_by('name', 'ASC')
			->find_all();

		$this->template->footer->js = View::factory('pages/person/js/edit');

		$person_id = $this->request->param('id', 0);
		$parent_id = (int) $this->request->query('parent_id');
		$person = ORM::factory('Person')
			->where('id', '=', $person_id)
			->where('user_id', '=', $this->user->id)
			->find();

		// If Parent ID is set, make sure it exists
		if ($parent_id)
		{
			$parent = ORM::factory('Person')
				->where('id', '=', $parent_id)
				->where('user_id', '=', $this->user->id)
				->find();

			if ( ! $parent->loaded() )
			{
				HTTP::redirect('dashboard');
			}
		}
		else
		{
			$parent = $person->parent;
		}

		if ( ! empty($_POST) )
		{
			$post = $_POST;
			print_r($post);
			$extra_validation = Validation::factory($post)
				->rule('contact', 'not_empty')
				->rule('contact', 'is_array')
				->rule('contact', array($this, 'valid_contact'), array(':value'));

			try
			{
				// 1. Save Names
				$person->values($post, array(
					'name',
					));
				$person->check($extra_validation);

				// Save parent_id only if this is the first time
				if ( ! $person->loaded() )
				{
					$person->parent_id = $parent_id;
				}
				$person->user_id = $this->user->id;
				$person->save();

				// 2. Save Group Info
				// Drop relationships first
				foreach ($person->groups->find_all() as $group)
				{
					$person->remove('groups', $group);
				}
				// Re-Attach Groups
				foreach ($post['group'] as $key => $group_id)
				{
					$group = ORM::factory('Group')
						->where('id', '=', (int) $group_id)
						->where('user_id', '=', $this->user->id)
						->find();

					if( $group->loaded() AND ! $person->has('groups', $group))
					{
						// Add Relationship
						$person->add('groups', $group);
					}
				}

				// 3. Delete A Contact
				foreach ($post['delete'] as $delete_id)
				{
					
					$contact = ORM::factory('Contact', (int) $delete_id);

					if( $contact->loaded() AND $person->has('contacts', $contact))
					{
						echo 'Removing '.$contact->id.'<br />';
						// Remove Relationship
						$person->remove('contacts', $contact);
					}
				}				

				// 4. Save Contact Info
				foreach ($post['contact'] as $key => $_contact)
				{
					// Clean Contact Before Comparing
					// ++TODO Setup a Sitewide function to do this
					$__contact = ($_contact['type'] == 'phone') ? 
						preg_replace("/[^0-9,.]/", "", $_contact['contact']) : 
						strtolower($_contact['contact']);

					$contact = ORM::factory('Contact')
						->where('id', '=', (isset($_contact['id'])) ? $_contact['id'] : 0)
						->where('type', '=', $_contact['type'])
						->where('contact', '=', $__contact)
						->find();

					if ( ! $contact->loaded() )
					{
						$contact->type = $_contact['type'];
						$contact->contact = $__contact;
						$contact->save();
					}
					
					if( ! $person->has('contacts', $contact))
					{
						// Add Relationship
						$person->add('contacts', $contact);
					}
				}

				// Redirect to prevent repost
				HTTP::redirect('person/edit/'.$person->id.'?done');
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = Arr::flatten($e->errors('models'));
			}
		}
		else
		{
			if ( $person->loaded() )
			{
				$post = array(
					'name' => $person->name,
					);

				// Get Person Groups
				foreach ($person->groups->find_all() as $group)
				{
					$post['group'][] = $group->id;
				}

				// Get Person Contacts
				foreach ($person->contacts->find_all() as $contact)
				{
					$post['contact'][] = array(
						'id' => $contact->id,
						'type' => $contact->type,
						'contact' => $contact->contact
						);
				}
			}

			$done = (isset($_GET['done'])) ? TRUE : FALSE;
		}
	}

	/**
	 * View Person
	 * 
	 * @return void
	 */
	public function action_view()
	{
		$this->template->content = View::factory('pages/person/view')
			->bind('person', $person)
			->bind('pings', $pings)
			->bind('pongs', $pongs)
			->bind('children', $children);

		$person_id = $this->request->param('id', 0);

		$person = ORM::factory('Person')
			->where('id', '=', $person_id)
			->where('user_id', '=', $this->user->id)
			->find();

		if ( ! $person->loaded() )
		{
			HTTP::redirect('dashboard');
		}

		$pings = ORM::factory('Ping')
			->select('contacts.contact')
			->join('contacts', 'INNER')->on('contacts.id', '=', 'ping.contact_id')
			->join('contacts_people', 'INNER')->on('contacts.id', '=', 'contacts_people.contact_id')
			->where('contacts_people.person_id', '=', $person->id)
			->order_by('created', 'DESC')
			->limit(10)
			->find_all();

		$pongs = ORM::factory('Pong')
			->select('contacts.contact')
			->join('contacts', 'INNER')->on('contacts.id', '=', 'pong.contact_id')
			->join('contacts_people', 'INNER')->on('contacts.id', '=', 'contacts_people.contact_id')
			->where('contacts_people.person_id', '=', $person->id)
			->order_by('created', 'DESC')
			->limit(10)
			->find_all();

		$children = $person->children->find_all();
	}

	/**
	 * Delete A Person
	 * 
	 * @return void
	 */
	public function action_delete()
	{
		$person_id = $this->request->param('id', 0);

		$person = ORM::factory('Person')
			->where('id', '=', $person_id)
			->where('user_id', '=', $this->user->id)
			->find();

		if ( $person->loaded() )
		{
			$person->delete();
			HTTP::redirect('dashboard');
		}
		else
		{
			HTTP::redirect('dashboard');
		}
	}

	/**
	 * Use datatables to generate dashboard list
	 */
	public function action_ajax_list()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		// Data table columns
		$columns = array('name', 'status', 'pings', 'last_name');

		$pings = DB::select('cp.person_id', array(DB::expr('COUNT(pings.id)'), 'pings'))
		    ->from('pings')
		    ->join(array('contacts', 'c'), 'INNER')
		    	->on('pings.contact_id', '=', 'c.id')
		    ->join(array('contacts_people', 'cp'), 'INNER')
		    	->on('c.id', '=', 'cp.contact_id')
		    ->group_by('cp.person_id');

		$query = ORM::factory('Person')
		    ->select('pings.pings')
		    ->join(array($pings, 'pings'), 'LEFT')
		    ->on('person.id', '=', 'pings.person_id')
		    ->where('user_id', '=', $this->user->id)
		    ->where('parent_id', '=', 0);

		$query2 = clone $query;

		// Searching & Filtering
		if (  isset( $_GET['sSearch'] ) AND $_GET['sSearch'] != "" )
		{
			$query->where_open();
			for ( $i=0 ; $i < count($columns) ; $i++ )
			{
				$query->or_where($columns[$i], 'LIKE', '%'.$_GET['sSearch'].'%');
			}
			$query->where_close();
		}

		// Paging
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$query->offset($_GET['iDisplayStart']);
			$query->limit($_GET['iDisplayLength']);
		}

		// Ordering
		if ( isset( $_GET['iSortCol_0'] ) AND $_GET['iSortCol_0'] != 0 )
		{
			$query->order_by($columns[$_GET['iSortCol_0']], $_GET['sSortDir_0']);
		}

		$people = $query->find_all()->as_array();

		//Output
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => count($people),
			"iTotalDisplayRecords" => $query2->count_all(),
			"aaData" => array()
		);

		foreach ($people as $person)
		{
			switch ($person->status) {
				case 'notok':
					$status_label = 'alert';
					break;

				case 'ok':
					$status_label = 'success';
					break;
				
				default:
					$status_label = 'secondary';
					break;
			}

			$row = array(
				0 => '<a href="/person/view/'.$person->id.'"><strong>'.strtoupper($person->name).'</strong></a>',
				1 => '<span class="radius '.$status_label.' label">'.strtoupper($person->status).'</status>',
				2 => '<span class="radius secondary label">'.(int) $person->pings.'</span>',
				);

			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}

	/**
	 * Valid Contact Type/Contact?
	 *
	 * @param array $contacts
	 * @return bool
	 */
	public function valid_contact($contacts)
	{
		foreach ($contacts as $contact)
		{
			if ( isset($contact['type']) AND isset($contact['contact']) )
			{
				if ( ! array_key_exists($contact['type'], PingApp_Form::contact_types(FALSE)))
				{
					return FALSE;
				}

				// @todo - provide better validation here
				if ( empty($contact['contact']) )
				{
					return FALSE;
				}
			}
			else
			{
				return FALSE;
			}
		}

		return TRUE;
	}
}
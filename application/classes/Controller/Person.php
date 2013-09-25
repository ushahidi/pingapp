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
			->bind('person', $person)
			->bind('post', $post)
			->bind('errors', $errors)
			->bind('done', $done);

		$this->template->footer->js = View::factory('pages/person/js/edit');

		$person_id = $this->request->param('id', 0);
		$person = ORM::factory('Person', $person_id);

		if ( ! empty($_POST) )
		{
			$post = $_POST;
			$extra_validation = Validation::factory($post)
				->rule('contact', 'not_empty')
				->rule('contact', 'is_array')
				->rule('contact', array($this, 'valid_contact'), array(':value'));

			try
			{
				// 1. Save Names
				$person->values($post, array(
					'first_name', 'last_name',
					));
				$person->check($extra_validation);
				$person->user_id = $this->user->id;
				$person->save();

				// 2. Save Contact Info
				// Empty Table First (To Handle Deletes)
				$_contacts = ORM::factory('Person_Contact')
					->where('person_id', '=', $person_id)
					->find_all();

				foreach ($_contacts as $_contact)
				{
					$_contact->delete();
				}

				foreach ($post['contact'] as $key => $_contact)
				{
					$contact = ORM::factory('Person_Contact')
						->where('id', '=', $key)
						->where('person_id', '=', $person_id)
						->find();

					$contact->type = $_contact['type'];
					$contact->contact = $_contact['contact'];
					$contact->person_id = $person->id;
					$contact->save();
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
					'first_name' => $person->first_name,
					'last_name' => $person->last_name,
					);

				// Get Person Contacts
				foreach ($person->person_contacts->find_all() as $contact)
				{
					$post['contact'][] = array(
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
			->bind('person', $person);

		$person_id = $this->request->param('id', 0);

		$person = ORM::factory('Person', $person_id);

		if ( ! $person->loaded() )
		{
			HTTP::redirect('dashboard');
		}
	}

	/**
	 * Valid Contact Type/Contact?
	 * 
	 * @return void
	 */
	public function valid_contact($contacts)
	{
		foreach ($contacts as $contact)
		{
			if ( isset($contact['type']) AND isset($contact['contact']) )
			{
				if ( ! array_key_exists($contact['type'], Pingapp_Form::contact_types(FALSE)))
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
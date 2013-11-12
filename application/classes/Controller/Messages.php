<?php defined('SYSPATH') or die('No direct access allowed');

class Controller_Messages extends Controller_PingApp {
	
	private $_provider = NULL;
	
	/**
	 * List of error messages
	 * @var array
	 */
	private $_errors = array();

	private $_post = NULL;
	
	/**
	 * Creates a new message
	 * @todo move sending to helper functions
	 */
	public function action_new()
	{
		$this->template->content = View::factory('pages/messages/new')
			->bind('user', $this->user)
			->bind('post', $this->_post)
			->bind('errors', $this->_errors)
			->bind('done', $done);

		$this->template->footer->js = View::factory('pages/messages/js/new');

		if ($this->request->method() === 'POST')
		{
			if ($this->_broadcast_message())
			{
				HTTP::redirect('messages/new?done');
			}
		}
		else
		{
			$done = (isset($_GET['done'])) ? TRUE : FALSE;
		}
	}

	/**
	 * List Messages
	 * 
	 * @return void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('pages/messages/index');
		$this->template->footer->js = View::factory('pages/messages/js/index');
	}

	/**
	 * View Message
	 * 
	 * @return void
	 */
	public function action_view()
	{
		$this->template->content = View::factory('pages/messages/view')
			->bind('message', $message);

		$this->template->footer->js = View::factory('pages/messages/js/view')
			->bind('message', $message);

		$message_id = $this->request->param('id', 0);

		$message = ORM::factory('Message')
			->where('id', '=', $message_id)
			->where('user_id', '=', $this->user->id)
			->find();

		if ( ! $message->loaded() )
		{
			HTTP::redirect('dashboard');
		}
	}

	/**
	 * Use datatables to generate messages list
	 */
	public function action_ajax_list()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		// Data table columns
		$columns = array('message', 'title', 'pings', 'message.created');

		$pings = DB::select('message_id', array(DB::expr('COUNT(pings.id)'), 'pings'))
			->from('pings')
			->group_by('message_id');

		$query = ORM::factory('Message')
		    ->select('pings.pings')
		    ->join(array($pings, 'pings'), 'LEFT')
		    	->on('message.id', '=', 'pings.message_id');

		if (  isset( $_GET['type'] ) AND $_GET['type'] != "" )
		{
			$query->where('message.type', '=', $_GET['type']);
		}

		// Pull Messages for a Specific User? If So, Make sure we're an admin
		$admin = FALSE;
		if (  isset( $_GET['user_id'] ) AND (int) $_GET['user_id'] 
			AND $this->user->has('roles', ORM::factory('Role')->where('name', '=', 'admin')->find() )
			)
		{
			$admin = TRUE;
			$query->where('user_id', '=', (int) $_GET['user_id']);
		}
		else
		{
			$query->where('user_id', '=', $this->user->id);
		}

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

		$messages = $query->find_all()->as_array();

		//Output
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => count($messages),
			"iTotalDisplayRecords" => $query2->count_all(),
			"aaData" => array()
		);

		foreach ($messages as $message)
		{
			$row = array(
				0 => '<a href="/messages/view/'.$message->id.'">'.Text::limit_chars($message->message, 30, '...').'</a>',
				1 => '<span class="radius secondary label">'.(int) $message->pings.'</span>',
				2 => date('Y-m-d g:i a', strtotime($message->created)),
				);

			if ($admin)
			{
				$row[0] = Text::limit_chars($message->message, 30, '...');
			}

			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}

	/**
	 * Calculate Send Expense
	 */
	public function action_ajax_calculate()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		$calculate = array(
			'sms' => 0,
			'email' => 0,
			'cost' => 0
			);

		$types = $this->request->post('type');
		$recipients = $this->request->post('recipients');
		if ( is_array($recipients) )
		{
			foreach ($types as $type)
			{
				// If EVERYONE is selected, ignore the others
				$operator = 'IN';
				if ( in_array(0, $recipients))
				{
					$operator = '>';
					$recipients = 0;
				}

				$count = ORM::factory('Contact')
					->join('contacts_people')->on('contact.id', '=', 'contacts_people.contact_id')
					->join('people')->on('people.id', '=', 'contacts_people.person_id')
					->where('people.user_id', '=', $this->user->id)
					->where('contact.type', '=', ($type == 'sms') ? 'phone' : $type)
					->where('people.id', $operator, $recipients)
					->count_all();

				$calculate[$type] = $count;
			}
		}

		echo json_encode($calculate);
	}

	/**
	 * Validates the input data and broadcasts the message
	 * via the configured SMS provider
	 *
	 * @return bool TRUE if successful, FALSE otherwise
	 */
	private function _broadcast_message()
	{
		$this->_post = $this->request->post();

		// Get the SMS provider to use
		try
		{
			$this->_provider = PingApp_SMS_Provider::instance();
		}
		catch (PingApp_Exception $e)
		{
			$this->_errors[] = $e->getMessage();
			Kohana::$log->add(Log::ERROR, $e->getMessage());
			return FALSE;
		}
		
		// Validate the recipients
		$recipients = $this->request->post('recipients');
		if ( ! is_array($recipients) )
		{
			$this->_errors[] = 'No recipients selected';
			return FALSE;
		}

		// Send Type Included?
		if ( ! $this->request->post('type') OR ! is_array($this->request->post('type')) )
		{
			$this->_errors[] = 'No message type selected';
			return FALSE;
		}

		// Type is Email or SMS?
		if ( ! in_array('email', $this->request->post('type')) AND ! in_array('sms', $this->request->post('type')))
		{
			$this->_errors[] = 'Message type must be SMS or Email';
			return FALSE;
		}
		
		// If EVERYONE is selected, ignore the others
		$operator = 'IN';
		if ( in_array(0, $recipients))
		{
			$operator = '>';
			$recipients = 0;
		}
		$contacts = ORM::factory('Contact')
			->join('contacts_people')->on('contact.id', '=', 'contacts_people.contact_id')
			->join('people')->on('people.id', '=', 'contacts_people.person_id')
			->where('people.user_id', '=', $this->user->id)
			->where('people.id', $operator, $recipients)
			->find_all();

		if ( ! $contacts->count() )
		{
			$this->_errors[] = 'No recipients to send to';
			return FALSE;
		}

		try
		{
			foreach ($this->request->post('type') as $type)
			{
				// Create the message
				$$type = ORM::factory('Message');

				// Set values and save
				${$type}->values(array(
						'type' => $type,
						'message' => $this->request->post('message'),
						'title' => $this->request->post('title'),
						'user_id' => $this->user->id
					));
				${$type}->check();
			}

			foreach ($this->request->post('type') as $type)
			{
				${$type}->save();

				// Save Ping
				foreach ($contacts as $contact)
				{
					$ping = ORM::factory('Ping');
					$ping->values(array(
							'message_id' => ${$type}->id,
							'tracking_id' => '0',
							'contact_id' => $contact->id,
							'provider' => 0,
							'status' => 'pending',
							'sent' => 0
						));

					if ($type == 'sms' AND $contact->type == 'phone')
					{
						$ping->type = $type;
						$ping->save();
					}

					if ($type == 'email' AND $contact->type == 'email')
					{
						$ping->type = $type;
						$ping->save();
					}
				}
			}
		}
		catch (ORM_Validation_Exception $e)
		{
			$this->_errors = Arr::flatten($e->errors('models'));
			Kohana::$log->add(Log::ERROR, $e->getMessage());

			return FALSE;
		}
		
		return TRUE;
	}
}
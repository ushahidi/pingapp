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
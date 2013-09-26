<?php defined('SYSPATH') or die('No direct access allowed');

class Controller_Messages extends Controller_PingApp {
	
	private $_provider = NULL;
	
	/**
	 * List of error messages
	 * @var array
	 */
	private $_errors = array();
	
	/**
	 * Creates a new message
	 * @todo move sending to helper functions
	 */
	public function action_new()
	{
		$this->template->content = View::factory('pages/send')
			->bind('user', $this->user)
			->bind('post', $post)
			->bind('errors', $this->_errors)
			->bind('done', $done);

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
		
		// If EVERYONE is selected, ignore the others
		$operator = 'IN';
		if ( in_array(0, $recipients))
		{
			$operator = '>';
			$recipients = 0;
		}
		$person_contacts = ORM::factory('Person_Contact')
			->where('type', '=', 'phone')
			->where('person_id', $operator, $recipients)
			->find_all();

		if ( ! $person_contacts->count() )
		{
			$this->_errors[] = 'None of your recipients have phone numbers to send to';
			return FALSE;
		}

		// Create the message
		$message = new Model_Message();
		try
		{
			// Set values and save
			$message->values(array(
					'message' => $this->request->post('message'),
					'user_id' => $this->user->id,
					'type' => 'phone'
				))
				->save();
			
			// Tracks the no. of pings sent out
			$ping_count = 0;
	
			$_columns = array('message_id', 'tracking_id', 'person_contact_id', 'provider', 'type', 'status', 'created');
			$query = DB::insert('pings', $_columns);

			
			// Ping!
			foreach ($person_contacts as $contact)
			{
				if (($tracking_id = $this->_provider->send(PingApp::$sms_sender, $contact->contact, $message->message)) !== FALSE)
				{
					$query->values(array(
					    'message_id' => $message->id,
					    'tracking_id' => $tracking_id,
					    'person_contact_id' => $contact->id,
					    'provider' => strtolower(PingApp::$sms_provider),
					    'type' => 'phone',
					    'status' => 'pending',
					    'created' => date('Y-m-d H:i:s')
					));
					$ping_count++;
				}
			}
			
			// Any pings go out?
			if ($ping_count)
			{
				try
				{
					// Create the pings
					$query->execute();
					Kohana::$log->add(Log::INFO, __("Successfully dispatched :count pings", array(":count" => $ping_count)));
				}
				catch (Database_Exception $e)
				{
					// Rollback message creation
					$messsage->delete();
					
					Kohana::$log->add(Log::ERROR, $e->getMessage());
					
					return FALSE;
				}
			}
			else
			{
				Kohana::$log->add(Log::INFO, "No messages sent");
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
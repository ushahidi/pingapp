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
		
		$query = ORM::factory('Person_Contact')
			->where('type', '=', 'phone');
		
		// If EVERYONE is selected, ignore the others
		if ( ! in_array(0, $recipients))
		{
			$query->where('id', 'IN', $recipients);
		}
		$person_contacts = $query->find_all();
		
		// Check if the fetched Person_Contact entries = provided recipients
		$_diff = count($recipients) - $person_contacts->count();
		if ( ! in_array(0, $recipients) AND $_diff > 0)
		{
			$this->_errors[] = __(":diff of your selected recipients could not be validated. Please try again",
			    array(":diff" => $_diff));

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
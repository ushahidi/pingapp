<?php defined('SYSPATH') or die('No direct access allowed');

class Controller_Messages extends Controller_PingApp {
	
	public function action_index()
	{
		$this->auto_render = FALSE;
		$this->template = '';

		switch ($this->request->method())
		{
			case 'POST':
				// Get the request parameters
				$message_text = $this->request->post('message_text');
				$recipients = $this->request->post('recipients');
				
				$recipients = explode(",", $recipients);
				
				$person_contact_ids = array();
				$person_contacts = ORM::factory('Person_Contact')
					->where('contact', 'IN', $recipients)
					->where('type', '=', 'phone')
					->find_all();
				
				foreach ($person_contacts as $contact)
				{
					$person_contact_ids[$contact->contact] = $contact->id;
				}

				// Create the messages
				$message = new Model_Message();
				$message->set('message', $message_text)
					->set('user_id', $this->user->id)
					->set('type', 'phone')
					->save();
				
				// Broadcast the message to recipients. The Ping
				$provider = PingApp_SMS_Provider::instance();
				$ping_count = 0;
				
				$query = DB::insert('pings',
				    array('message_id', 'tracking_id', 'person_contact_id', 'provider', 'type', 'status', 'created'));
					
				foreach ($recipients as $recipient)
				{
					if (($tracking_id = $provider->send(PingApp::$sms_sender, $recipient, $message->message)) !== FALSE)
					{
						$query->values(array(
						    'message_id' => $message->id,
						    'tracking_id' => $tracking_id,
						    'person_contact_id' => $person_contact_ids[$recipient],
						    'provider' => strtolower(PingApp::$sms_provider),
						    'type' => 'phone',
						    'status' => 'pending',
						    'created' => date('Y-m-d H:i:s')
						));
							$ping_count++;
					}
				}
				
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
						Kohana::$log->add(Log::ERROR, $e->getMessage());
					}
				}
				else
				{
					Kohana::$log->add(Log::INFO, "No messages sent");
				}
				
				HTTP::redirect('messages/new');
			break;
			
		}
	}
	
	/**
	 * Creates a new message
	 */
	public function action_new()
	{
		$this->template->content = View::factory('pages/send');
	}
}
<?php defined('SYSPATH') or die('No direct access allowed');

class Controller_Messages extends Controller_PingApp {
	
	private $_provider = NULL;
	
	/**
	 * Creates a new message
	 * @todo move sending to helper functions
	 */
	public function action_new()
	{
		$this->template->content = View::factory('pages/send')
			->bind('user', $this->user)
			->bind('post', $post)
			->bind('errors', $errors)
			->bind('done', $done);

		if ( ! empty($_POST) )
		{
			$post = $_POST;

			// Broadcast the message to recipients. The Ping
			try
			{
				$this->_provider = PingApp_SMS_Provider::instance();

				$extra_validation = Validation::factory($post)
					->rule('recipients', 'not_empty')
					->rule('recipients', 'is_array')
					->rule('recipients', array($this, 'valid_recipients'), array(':value'))
					->rule('token', 'Security::check');

				try
				{
					// Save the messages
					$message = ORM::Factory('Message')->values($post, array(
						'message',
						));
					$message->check($extra_validation);
					$message->type = 'phone';
					$message->user_id = $this->user->id;
					$message->save();

					$recipients = $this->request->post('recipients');
					
					$person_contact_ids = array();
					$person_contacts = ORM::factory('Person_Contact')
						->where('type', '=', 'phone');
					// If EVERYONE is selected, ignore the others
					if ( ! in_array(0, $recipients))
					{
						$person_contacts->where('contact', 'IN', $recipients);
					}
					$person_contacts->find_all();
					
					foreach ($person_contacts as $contact)
					{
						$person_contact_ids[$contact->contact] = $contact->id;
					}
					
					$ping_count = 0;
					
					$query = DB::insert('pings',
					    array('message_id', 'tracking_id', 'person_contact_id', 'provider', 'type', 'status', 'created'));
						
					foreach ($recipients as $recipient)
					{
						if (($tracking_id = $this->_provider->send(PingApp::$sms_sender, $recipient, $message->message)) !== FALSE)
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
					
					HTTP::redirect('messages/new?done');
				}
				catch (ORM_Validation_Exception $e)
				{
					$errors = Arr::flatten($e->errors('models'));
				}
			}
			catch (PingApp_Exception $e)
			{
				Kohana::$log->add(Log::ERROR, $e->getMessage());
				
				// TODO: Set error messages
				$errors = Arr::flatten($e->errors('models'));
				
				// Redirect
				//HTTP::redirect('/messages/new');
			}
		}
		else
		{
			$done = (isset($_GET['done'])) ? TRUE : FALSE;
		}
	}

	/**
	 * Valid Recipients
	 *
	 * @param array $recipients
	 * @return bool
	 */
	public function valid_recipients($recipients)
	{
		if ( ! count($recipients))
		{
			return FALSE;
		}

		foreach ($recipients as $recipient)
		{
			if ($recipient == 0)
			{
				continue;
			}

			$person = ORM::factory('Person')
				->where('user_id', '=', $this->user->id)
				->where('id', '=', $recipient)
				->find();

			if ( ! $person->loaded() )
			{
				return FALSE;
			}
		}

		return TRUE;
	}
}
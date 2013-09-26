<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Sms_Twilio extends Controller {
	
	public function action_reply()
	{
		if ($this->request->method() == 'POST')
		{
			$to = $this->request->post('To');
			$from  = $this->request->post('From');
			
			if ($to !== PingApp::$sms_sender)
			{
				Kohana::$log->add(Log::ERROR, __("':to' was not used to send a message to ':from'",
				    array(':to' => $to, ':from' => $from)));
				return;
			}
			
			$message_text  = $this->request->post('Body');
			
			$contact = Model_Contact::get_contact($from, 'phone');
			if ( ! $contact)
			{
				// HALT
				Kohana::$log->add(Log::ERROR, __("':from' is not registered as a contact", array(":from" => $from)));
				return;
			}

			// Lets find out if this was a response to a ping we
			// sent before
			$ping = ORM::factory('Ping')
				->where('provider', '=', strtolower(PingApp::$sms_provider))
				->where('type', '=', 'phone')
				->where('contact_id', '=', $contact->id)
				->find();

			// Looks like we pinged this number
			if ( $ping->loaded() )
			{
				// Record the pong
				$pong = new Model_Pong();
				$pong->set('content', $message_text)
				    ->set('contact_id', $contact->id)
				    ->set('type', 'phone')
				    ->save();

				// Lets parse the message for OK/NOT OKAY indicators
				PingApp_Parse::status($contact, $pong, $message_text);
			}
			// Looks like this is SPAM
			else
			{
				Kohana::$log->add(Log::ERROR, __("No ping sent out to ':from'. Discarding message", array(":from" => $from)));
			}
		}
	}
}
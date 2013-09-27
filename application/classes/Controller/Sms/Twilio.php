<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Sms_Twilio extends Controller {
	
	public function action_reply()
	{
		if ($this->request->method() == 'POST')
		{
			$provider = PingApp_SMS_Provider::instance();
			
			// Authenticate the request
			$options =  $provider->options();
			if ($this->request->post('AccountSid') !== $options['account_sid'])
			{
				// Could not authenticate the request?
				throw new HTTP_Exception_403();
			}
			
			$to = $this->request->post('To');
			$from  = $this->request->post('From');

			if ( ! $to OR $to !== $sender)
			{
				Kohana::$log->add(Log::ERROR, __("':to' was not used to send a message to ':from'",
				    array(':to' => $to, ':from' => $from)));
				return;
			}
			
			$message_text  = $this->request->post('Body');
	
			// Is the sender of the message a registered contact?
			$contact = Model_Contact::get_contact($from, 'phone');
			if ( ! $contact)
			{
				// HALT
				Kohana::$log->add(Log::ERROR, __("':from' is not registered as a contact", array(":from" => $from)));
				return;
			}
			
			// Use the last id of the ping to tag the pong
			// TODO: Review
			$ping = DB::query(array(DB::expr('COUNT(id)'), 'ping_id'))
				->from('pings')
				->where('contact_id', '=', $contact->id)
				->where('type', '=', 'phone')
				->where('status', 'pending')
				->execute()
				->as_array();
			
			// Record the pong
			if ( ! count($ping))
			{
				// Load the pong
				$ping = ORM::factory('Ping', $ping[0]['ping_id']);
				
				// Mark the ping as replied
				$ping->set('status', 'replied')->save();
				
				$pong = ORM::factory('Pong')
					->values(array(
						'content' => $message_text,
						'contact_id' => $contact->id,
						'type' => 'phone',
						'ping_id' => $ping->id
					))
					->save();
				
				// Lets parse the message for OK/NOT OKAY indicators
				PingApp_Parse::status($contact, $pong, $message_text);
			}
			else
			{
				Kohana::$log->add(Log::ERROR, __("There is no record of ':from' having been pinged",
					array(":from" => $from)));
			}
		}
,	}
}
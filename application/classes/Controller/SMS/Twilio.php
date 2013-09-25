<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_SMS_Twilio extends Controller {
	
	public function action_reply()
	{
		if ($this->request->method() == 'POST')
		{
			$to = $this->request->post('To');
			$from  = $this->request->post('From');
			
			if (! $to === Pingapp::$sms_sender)
			{
				Kohana::$log->add(Log::ERROR, __("':to' was not used to send a message to ':from'",
				    array(':to' => $to, ':from' => $from)));
				return;
			}
			
			$message_text  = $this->request->post('Body');
			$message_date = $this->request->post('Date');
			
			$person_contact = Model_Person::get_contact($from, 'phone');
			if ( ! $person_contact)
			{
				// HALT
				Kohana::$log->add(Log::ERROR, __("':from' is not registered as a contact", array(":from" => $from)));
				return;
			}
			
			// Get the last message that was sent to $from via Twilio
			// and has a pending status
			$ping = ORM::factory('ping')
			    ->where('provider', '=', strtolower(Pingapp::$sms_provider))
			    ->where('type', '=', 'phone')
			    ->where('person_contact_id', '=', $person_contact->id)
			    ->where('status', '=', 'pending')
			    ->find();
			
			// Any result?
			if ( ! $ping->loaded())
			{
				Kohana::$log->add(Log::ERROR, __("No ping sent out to ':from'. Discarding message", array(":from" => $from)));
			}
			else
			{
				// Mark the ping as having received a response
				$ping->set('status', 'replied')->save();
				
				// Record the pong
				$pong = new Model_Pong();
				$pong->set('person_id', $person_contact->person->id)
				    ->set('ping_id', $ping->id)
				    ->set('contact', $message)
				    ->set('type', 'sms')
				    ->save();
				
			}
		}
	}
}
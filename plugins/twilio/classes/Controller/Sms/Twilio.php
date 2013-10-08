<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Sms_Twilio extends Controller {
	
	public function action_reply()
	{
		if ($this->request->method() == 'POST')
		{
			$provider = PingApp_SMS_Provider::instance();

			// Authenticate the request
			$options = $provider->options($provider::$sms_provider);
			if ($this->request->post('AccountSid') !== $options['account_sid'])
			{
				// Could not authenticate the request?
				throw new HTTP_Exception_403();
			}
			
			// Remove Non-Numeric characters because that's what the DB has
			$to = preg_replace("/[^0-9,.]/", "", $this->request->post('To'));
			$from  = preg_replace("/[^0-9,.]/", "", $this->request->post('From'));
			$sender = preg_replace("/[^0-9,.]/", "", $provider->from($provider::$sms_provider));

			if ( ! $to OR strrpos($to, $sender) === FALSE )
			{
				Kohana::$log->add(Log::ERROR, __("':to' was not used to send a message to ':from'",
				    array(':to' => $to, ':from' => $from)));
				return;
			}
			
			$message_text  = $this->request->post('Body');

			$provider->receive($from, $message_text);
		}
	}
}
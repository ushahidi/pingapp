<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Sms_Nexmo extends Controller {
	
	public function action_reply()
	{
		include_once Kohana::find_file('vendor', 'nexmo/NexmoMessage');
		
		// Nexmo Subnets
		// To Restrict Inbound Callback
		$subnets = array('174.37.245.32/29', '174.37.245.32/29', '174.36.197.192/28', '173.193.199.16/28', '119.81.44.0/28');

		// Pong Sender
		$ip_address = $_SERVER["REMOTE_ADDR"];
		$continue = FALSE;
		foreach ($subnets as $subnet)
		{
			if ( (PingApp_Pong::ip_in_range($ip_address, $subnet)) )
			{
				$continue = TRUE;
				break;
			};
		}

		if ( ! $continue)
		{
			// Could not authenticate the request?
			throw new HTTP_Exception_403();
		}

		$provider = PingApp_SMS_Provider::instance('nexmo');
		$options = $provider->options();

		// Authenticate the request
		$sms = new NexmoMessage($options['api_key'], $options['api_secret']);

		if ( ! $sms->inboundText())
		{
			// Could not authenticate the request?
			throw new HTTP_Exception_403();
		}
		
		// Remove Non-Numeric characters because that's what the DB has
		$to = preg_replace("/[^0-9,.]/", "", $sms->to);
		$from  = preg_replace("/[^0-9,.]/", "", $sms->from);
		$sender = $provider->from();

		if ( ! $to OR strrpos($to, $sender) === FALSE )
		{
			Kohana::$log->add(Log::ERROR, __("':to' was not used to send a message to ':from'",
			    array(':to' => $to, ':from' => $from)));
			return;
		}

		$provider->receive($from, $sms->text);
	}
}
<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Twilio SMS provider
 */
class Pingapp_SMS_Provider_Twilio extends Pingapp_SMS_Provider {
	
	/**
	 * Client to talk to the Twilio API
	 *
	 * @var Services_Twilio
	 */
	private $_client;

	/**
	 * @return mixed
	 */
	public function send($from, $to, $message)
	{
		include_once Kohana::find_file('vendor', 'twilio/sdk/Services/Twilio');
		
		if ( ! isset($this->_client))
		{
			$this->_client = new Services_Twilio($this->params['account_sid'], $this->params['auth_token']);
		}
		
		// Get the sender number
		$from = $this->params['sender'];
		
		// Send!
		$message = $this->_client->account->messages->sendMessage($from, $to, $message);
		
		return $message->sid;
	}
}
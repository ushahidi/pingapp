<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Sms_Smssync extends Controller {

	public function action_index()
	{
		$success = FALSE;
		$messages = array();

		if ($this->request->method() == 'POST')
		{
			$provider = PingApp_SMS_Provider::instance('smssync');

			// Authenticate the request
			$options = $provider->options();
			if ($this->request->post('secret') AND 
				$this->request->post('secret') == $options['secret'])
			{
				// Remove Non-Numeric characters because that's what the DB has
				$to = preg_replace("/[^0-9,.]/", "", $this->request->post('sent_to'));
				$from = preg_replace("/[^0-9,.]/", "", $this->request->post('from'));
				$message_text = $this->request->post('message');
				$sender = $provider->from();

				// If receiving an SMS Message
				if ($to AND $from AND $message_text)
				{
					$success = TRUE;
					$provider->receive($from, $message_text);
				}

				// Do we have any tasks for SMSSync?
				// 
				// Get All "Sent" SMSSync Pings
				// Limit it to 20 MAX and FIFO
				$pings = ORM::factory('Ping')
					->select('c.contact')
					->select('m.message')
					->join(array('contacts', 'c'), 'INNER')
						->on('ping.contact_id', '=', 'c.id')
					->join(array('messages', 'm'), 'INNER')
						->on('ping.message_id', '=', 'm.id')
					->where('status', '=', 'pending')
					->where('sent', '=', 1)
					->where('provider', '=', 'smssync')
					->order_by('created', 'ASC')
					->limit(20)
					->find_all();

				foreach ($pings as $ping)
				{
					$messages[] = array(
						'to' => $ping->contact,
						'message' => $ping->message
						);

					// We'll update the pings status to 'unknown'
					// just so that its not picked up again
					// Also we don't know if the SMSSync from the phone
					// itself worked or not
					$ping->status = 'unknown';
					$ping->save();
				}
				$success = TRUE;
			}

			$json = array(
				'payload' => array(
					'success' => $success,
					'messages' => $messages
					)
				);

			echo json_encode($json);
		}
	}
}
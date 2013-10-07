<?php

// Plugin Info
$plugin = array(
	'twilio' => array(
		'name' => 'Twilio',
		'version' => '0.1',

		// Services Provided By This Plugin
		'services' => array(
			'sms' => true,
			'ivr' => true,
			'email' => false
		),

		// Option Key and Label
		'options' => array(
			'phone' => 'Phone Number', 
			'account_sid' => 'Account SID',
			'auth_token' => 'Auth Token'
		),

		// Links
		'links' => array(
			'developer' => 'https://www.twilio.com',
			'signup' => 'https://www.twilio.com/try-twilio'
		)
	)
);

// Register the plugin
Event::instance()->fire('PingApp_Plugin', array($plugin));

// Additional Routes
<?php

// Plugin Info
$plugin = array(
	'nexmo' => array(
		'name' => 'Nexmo',
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
			'api_key' => 'API Key',
			'api_secret' => 'API Secret'
		),

		// Links
		'links' => array(
			'developer' => 'https://www.nexmo.com/',
			'signup' => 'https://dashboard.nexmo.com/register'
		)
	)
);

// Register the plugin
Event::instance()->fire('PingApp_Plugin', array($plugin));

// Additional Routes
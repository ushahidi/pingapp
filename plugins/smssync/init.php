<?php

// Plugin Info
$plugin = array(
	'smssync' => array(
		'name' => 'SMSSync',
		'version' => '0.1',

		// Services Provided By This Plugin
		'services' => array(
			'sms' => true,
			'ivr' => false,
			'email' => false
		),

		// Option Key and Label
		'options' => array(
			'phone' => 'Phone Number', 
			'secret' => 'Secret',
		),

		// Links
		'links' => array(
			'developer' => 'http://smssync.ushahidi.com/',
			'signup' => 'http://smssync.ushahidi.com/'
		)
	)
);

// Register the plugin
Event::instance()->fire('PingApp_Plugin', array($plugin));

// Additional Routes
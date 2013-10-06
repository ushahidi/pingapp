<?php defined('SYSPATH') or die('No direct script access');

final class PingApp {
	
	/**
	 * SMS Service
	 * @var string
	 */
	public static $sms = FALSE;

	/**
	 * Name of the SMS provider
	 * @var string
	 */
	public static $sms_provider = NULL;
	
	/**
	 * Initializes Pingapp and Plugins
	 */
	public static function init()
	{
		/**
		 * Plugin Registration Listener
		 * ++TODO's
		 * 	  - Load Modules Here Instead of Boostrap
		 * 	  - Add to a plugins table to set on/off
		 * 	  - If off, unload from modules
		 */
		Event::instance()->listen(
			'PingApp_Plugin',
			function ($event, $params) {
				self::register($params);
			}
		);

		// SMS Settings
		self::$sms = (PingApp_Settings::get('sms') == 'on') ? TRUE : FALSE;
		self::$sms_provider = PingApp_Settings::get('sms_provider');
	}

	/**
	 * Register A Plugin
	 */
	public static function register($params)
	{
		try
		{
			$config = Kohana::$config->load('_plugins');
			$config->set(key($params), $params[key($params)]);	
		}
		catch (Exception $e)
		{
			// Problem Registering Config
		}
	}
}
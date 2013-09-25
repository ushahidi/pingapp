<?php defined('SYSPATH') or die('No direct access allowed'); 

/**
 * Base class for all SMS providers
 *
 * @author     Ushahidi Dev Team, Emmanuel Kala <emkala(at)gmail.com>
 * @package    PingApp - http://ping.ushahidi.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 *
 */
abstract class PingApp_SMS_Provider {
	
	/**
	 * Authentication parameters for the default SMS provider
	 * @var array
	 */
	protected $_options = array();
	
	protected static $_instance = NULL;
	
	public static function instance()
	{
		if (isset(self::$_instance))
		{
			return $_instance;
		}
		
		if (empty(PingApp::$sms_provider))
		{
			throw new PingApp_Exception("SMS Provider not configured");
		}

		$provider_name = ucfirst(strtolower(PingApp::$sms_provider));
		
		$class_name = 'PingApp_SMS_Provider_'.$provider_name;
		
		if ( ! class_exists($class_name))
		{
			throw new PingApp_Exception(__("Implementation for ':provider' SMS provider not found",
			    array(":provider" => $provider_name)));
		}
		
		// Create an instance of the SMS provider
		self::$_instance = new $class_name;
		
		// Check if the provider is a subclass of PingApp_SMS_Provider
		if ( ! is_a(self::$_instance, 'PingApp_SMS_Provider'))
		{
			throw new PingApp_Exception(__("':class' must extend the PingApp_SMS_Provider class",
				array(":provider" => $class_name)));
		}
		
		self::$_instance->set_options(PingApp::$sms_provider_options);
		return self::$_instance;
	}
	
	/**
	 * Sets the authentication parameters for the SMS provider
	 *
	 * @param  array auth_params
	 */
	public function set_options($options)
	{
		$this->_options = $options;
	}
	
	/**
	 * @param  string  from  Phone number sending the message
	 * @param  string  to    Phone number to receive the message
	 * @param  string  message Message to be sent
	 */
	abstract public function send($from, $to, $message);
}
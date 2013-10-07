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
	 * SMS [FROM] Number
	 * @var array
	 */
	protected $_from = null;

	/**
	 * Authentication parameters for the default SMS provider
	 * @var array
	 */
	protected $_options = array();
	
	protected static $_instance = NULL;
	
	public static function instance()
	{
		// SMS Settings
		self::$sms = (PingApp_Settings::get('sms') == 'on') ? TRUE : FALSE;
		self::$sms_provider = PingApp_Settings::get('sms_provider');

		if (isset(self::$_instance))
		{
			return $_instance;
		}
		
		if ( ! self::$sms OR empty(self::$sms_provider))
		{
			throw new PingApp_Exception("The SMS service is unavailable at this time. No SMS provider has been configured for use.");
		}

		$provider_name = ucfirst(strtolower(self::$sms_provider));
		
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

		// Get From
		self::$_instance->from(self::$sms_provider);

		// Get provider options
		self::$_instance->options(self::$sms_provider);
		return self::$_instance;
	}

	/**
	 * Sets the FROM parameter for the SMS provider
	 *
	 * @param  string sms provider
	 * @return void
	 */
	public function from($sms_provider)
	{
		// Get provider phone (FROM)
		// Replace non-numeric
		$this->_from = preg_replace("/[^0-9,.]/", "", PingApp_Settings::get(self::$sms_provider.'_phone'));
	}
	
	/**
	 * Sets the authentication parameters for the SMS provider
	 *
	 * @param  string sms provider
	 * @return void
	 */
	public function options($sms_provider)
	{
		$options = Kohana::$config->load('_plugins.'.$sms_provider.'.options');
		if (is_array($options))
		{
			foreach ($options as $key => $value)
			{
				$this->_options[$key] = PingApp_Settings::get($sms_provider.'_'.$key);
			}
		}
	}
	
	/**
	 * @param  string  to Phone number to receive the message
	 * @param  string  message Message to be sent
	 */
	abstract public function send($to, $message);
}
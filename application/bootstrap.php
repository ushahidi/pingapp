<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/Kohana/Core'.EXT;

if (is_file(APPPATH.'classes/Kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/Kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/Kohana'.EXT;
}

/**
 * Set the default time zone.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/timezones
 */
date_default_timezone_set('America/New_York');

/**
 * Set the default locale.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/function.setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Optionally, you can enable a compatibility auto-loader for use with
 * older modules that have not been updated for PSR-0.
 *
 * It is recommended to not enable this unless absolutely necessary.
 */
//spl_autoload_register(array('Kohana', 'auto_load_lowercase'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

/**
 * Set the mb_substitute_character to "none"
 *
 * @link http://www.php.net/manual/function.mb-substitute-character.php
 */
mb_substitute_character('none');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

if (isset($_SERVER['SERVER_PROTOCOL']))
{
	// Replace the default protocol.
	HTTP::$protocol = $_SERVER['SERVER_PROTOCOL'];
}

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name 'development' will be used instead
 */
if (($env = getenv('KOHANA_ENV')) === FALSE OR defined('Kohana::'.strtoupper($env)) === FALSE)
{
	$env = 'development';
}

// Ignoring code standards error about constant case
// @codingStandardsIgnoreStart
Kohana::$environment = constant('Kohana::'.strtoupper($env));
// @codingStandardsIgnoreEnd

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config = new Config;
Kohana::$config->attach(new Config_File);

/**
 * Attach the environment specific configuration file reader to config
 */
Kohana::$config->attach(new Config_File('config/environments/'.$env));

/**
 * Initialize Kohana, setting the default options.
 */
Kohana::init(Kohana::$config->load('init')->as_array());

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(Kohana::$config->load('modules')->as_array());

/**
 * Initialize Pingapp, setting the defaults
 */
PingApp::init();

/**
 * Enable plugins. Plugins are referenced by a relative or absolute path.
 */
//Kohana::modules( array_merge(Kohana::$config->load('plugins')->as_array(), Kohana::modules()) );

/**
 * Set cookie salt
 * @TODO change this for your project
 */
Cookie::$salt = 'pingapp-insecure-please-change-me';

/**
 * Register Predis and Autoload
 */
if ($path = Kohana::find_file('../vendor', 'predis/predis/lib/Predis/Autoloader'))
{
	require_once $path;
	Predis\Autoloader::register();
}

/**
 * Logout Route
 */	
Route::set('logout', 'logout')
	->defaults(array(
		'controller' => 'login',
		'action'     => 'logout',
	));

Route::set('sms_callback_url', '<directory>(/<controller>(/<action>))', array('directory' => 'sms'));
Route::set('ivr_callback_url', '<directory>(/<controller>(/<action>))', array('directory' => 'ivr'));

/**
 * Default Route
 */
Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'dashboard',
		'action'     => 'index',
	));
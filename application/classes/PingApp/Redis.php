<?php defined('SYSPATH') or die('No direct script access.');
/**
 * A Predis Redis Wrapper for PingApp
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Classes
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class PingApp_Redis {

	/**
	 * single instance of the Redis object
	 *
	 * @var Redis
	 **/
	private static $redis;

	/**
	 * Returns the singleton instance of Redis. If no instance has
	 * been created, a new instance will be created.
	 *       
	 *     $redis = PingApp_Redis::factory();
	 *
	 * @return PingApp_Redis
	 **/
	public static function factory()
	{
		if ( ! PingApp_Redis::$redis)
		{
			// No config file found
			if ( ! Kohana::$config->load('redis'))
			{	
				PingApp_Redis::$redis = new Predis\Client(array(
						'scheme' => 'tcp',
						'host'   => '127.0.0.1',
						'port'   => 6379,
					));
			}
			else
			{
				// Load config
				$config = Kohana::$config->load('redis');

				$host     = isset($config['host']) && ($config['host']) ? $config['host'] : '127.0.0.1'; 
				$port     = isset($config['port']) && ($config['port']) ? $config['port'] : 6379;

				PingApp_Redis::$redis = new Predis\Client(array(
						'scheme' => 'tcp',
						'host'   => $host,
						'port'   => $port,
					));
			}
		}

		return PingApp_Redis::$redis;
	}
}
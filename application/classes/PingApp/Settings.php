<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Settings Helper
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Classes
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class PingApp_Settings {

	/**
	 * Set
	 *
	 * @param string $key settings key
	 * @param string $value settings value
	 * @param object $user user to attach this setting to
	 * @return void
	 */
	public static function set($key = NULL, $value = NULL, $user = NULL)
	{
		if ($key)
		{
			// 0 = Global
			$user_id = ($user AND $user->loaded()) ? $user->id : 0;

			$setting = ORM::factory('Setting')
				->where('key', '=', $key)
				->where('user_id', '=', $user_id)
				->find();

			$setting->user_id = $user_id;
			$setting->key = $key;
			$setting->value = $value;
			$setting->save();
		}
	}

	/**
	 * Get
	 *
	 * @param string $key settings key
	 * @param object $user user that owns this setting
	 */
	public static function get($key = NULL, $user = NULL)
	{
		if ($key)
		{
			// 0 = Global
			$user_id = ($user AND $user->loaded()) ? $user->id : 0;

			$setting = ORM::factory('Setting')
				->where('key', '=', $key)
				->where('user_id', '=', $user_id)
				->find();

			if ($setting->loaded())
			{
				return $setting->value;
			}
		}

		return FALSE;
	}
}
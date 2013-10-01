<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Form Helper
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Classes
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class PingApp_Form {
	/**
	 * Contact Methods
	 *
	 * @param bool $select add select?
	 * @return	array
	 */
	public static function contact_types($select = TRUE)
	{
		$array = array(
			'phone' => __('Cell Phone'),
			'email' => __('Email'),
			'twitter' => __('Twitter'),
			'whatsapp' => __('WhatsApp'),
			);

		if ($select)
		{
			array_unshift($array, array(
				NULL => __('-- Select One --'),
				));
		}

		return $array;
	}

	/**
	 * Get People for Send Form
	 * 
	 * @param int $user_id
	 * @return array
	 */
	public static function people($user)
	{
		$array = array(
			'0' => '-- EVERYONE --'
			);

		if ( $user->loaded() )
		{
			$people = $user->people
				->where('parent_id', '=', 0)
				->order_by('name', 'ASC')
				->find_all();

			foreach ($people as $person)
			{
				$array[$person->id] = $person->name;
			}
		}

		return $array;
	}
}
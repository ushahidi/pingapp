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
}
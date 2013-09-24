<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Form Helper
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Classes
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Pingapp_Form {
	/**
	 * Contact Methods
	 * 
	 * @return	array
	 */
	public static function contact_types()
	{
		$array = array(
			NULL => __('-- Select One --'),
			'phone' => __('Cell Phone'),
			'email' => __('Email'),
			'twitter' => __('Twitter'),
			'whatsapp' => __('WhatsApp'),
			);

		return $array;
	}
}
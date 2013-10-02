<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Settings
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Setting extends ORM {
	/**
	 * A setting belongs to a user
	 */
	protected $_belongs_to = array(
		'settings' => array()
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
	protected $_updated_column = array('column' => 'updated', 'format' => 'Y-m-d H:i:s');

	/**
	 * Rules
	 */
	public function rules()
	{
		return array(
			'key' => array(
				array('not_empty'),
				array('min_length', array(':value', 3)),
				array('max_length', array(':value', 100)),
			)
		);
	}

	/**
	 * Filters
	 *
	 * @return array Rules
	 */
	public function filters()
	{
		return array(
			'key' => array(
				array('trim'),
			),
			'value' => array(
				array('trim'),
			),
		);
	}
}
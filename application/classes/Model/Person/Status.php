<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Person_Statuses
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Person_Status extends ORM {
	/**
	 * A person_status belongs to a person
	 */
	protected $_belongs_to = array(
		'person' => array(),
		'pong' => array(),
		'user' => array()
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
	protected $_updated_column = array('column' => 'updated', 'format' => 'Y-m-d H:i:s');

	/**
	 * Rules
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'person_id' => array(
				array('not_empty'),
				array('numeric'),
			),
			'note' => array(
				array('max_length', array(':value', 255)),
			),
			'status' => array(
				array('not_empty'),
				array('in_array', array(':value', array('ok', 'notok', 'unknown')) ),
			),
		);
	}

	/**
	 * Filters
	 *
	 * @return array Filters
	 */
	public function filters()
	{
		return array(
			'note' => array(
				array('trim'),
			),
			'status' => array(
				array('trim'),
			),
		);
	}
}
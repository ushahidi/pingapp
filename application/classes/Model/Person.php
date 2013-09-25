<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for People
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Person extends ORM {
	/**
	 * A person has many pings, person_contacts, children
	 */
	protected $_has_many = array(
		'pings' => array(),
		'pongs' => array(),
		'person_contacts' => array(),
		'children' => array(
			'model' => 'Person',
			'foreign_key' => 'parent_id',
			),
		);

	/**
	 * A person belongs to a parent, user
	 */
	protected $_belongs_to = array(
		'user' => array(),
		'parent' => array(
			'model'  => 'Person',
			'foreign_key' => 'parent_id',
			),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
	protected $_updated_column = array('column' => 'updated', 'format' => 'Y-m-d H:i:s');

	public function rules()
	{
		return array(
			'first_name' => array(
				array('not_empty'),
				array('min_length', array(':value', 2)),
				array('max_length', array(':value', 100)),
			),
			'last_name' => array(
				array('not_empty'),
				array('min_length', array(':value', 2)),
				array('max_length', array(':value', 100)),
			),
		);
	}
}
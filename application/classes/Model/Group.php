<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Groups
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Group extends ORM {
	/**
	 * A group has many people
	 */
	protected $_has_many = array(
		'people' => array(),
		);

	/**
	 * A group belongs to a user
	 */
	protected $_belongs_to = array(
		'user' => array(),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
	protected $_updated_column = array('column' => 'updated', 'format' => 'Y-m-d H:i:s');
}
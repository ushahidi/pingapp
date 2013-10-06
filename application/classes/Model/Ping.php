<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Pings
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Ping extends ORM {
	/**
	 * A ping belongs to a message, and a contact
	 */
	protected $_belongs_to = array(
		'message' => array(),
		'contact' => array(),
		);

	/**
	 * A ping has many pongs
	 */
	protected $_has_many = array(
		'pongs' => array(),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
	protected $_updated_column = array('column' => 'updated', 'format' => 'Y-m-d H:i:s');

	public function rules()
	{
		return array(
			'status' => array(
				array('in_array', array(':value', array('pending', 'sent', 'received', 'replied')) ),
			),
		);
	}
}
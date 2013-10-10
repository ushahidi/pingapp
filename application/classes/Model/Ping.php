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
	 * A ping also belongs to a parent ping (retries)
	 */
	protected $_belongs_to = array(
		'message' => array(),
		'contact' => array(),
		'parent' => array(
			'model'  => 'Ping',
			'foreign_key' => 'parent_id',
			),
		);

	/**
	 * A ping has many pongs and children pings (retries)
	 */
	protected $_has_many = array(
		'pongs' => array(),
		'children' => array(
			'model' => 'Ping',
			'foreign_key' => 'parent_id',
			),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
	protected $_updated_column = array('column' => 'updated', 'format' => 'Y-m-d H:i:s');

	public function rules()
	{
		return array(
			'message_id' => array(
				array('not_empty'),
				array('numeric'),
				array(array($this, 'valid_id'), array(':validation', ':field')),
			),
			'contact_id' => array(
				array('not_empty'),
				array('numeric'),
				array(array($this, 'valid_id'), array(':validation', ':field')),
			),
			'type' => array(
				array('not_empty'),
				array('in_array', array(':value', array('sms', 'email', 'twitter')) ),
			),
			'status' => array(
				array('in_array', array(':value', array('pending', 'received', 'replied', 'expired', 'failed', 'cancelled', 'unknown')) ),
			),
			'sent' => array(
				array('in_array', array(':value', array(0, 1)) ),
			),
		);
	}

	/**
	 * Validate ID
	 *
	 * @param array $validation
	 * @param string $field field name
	 * @return void
	 */
	public function valid_id($validation, $field)
	{
		if ( ! (int) $validation[$field] > 0)
		{
			$validation->error($field, 'invalid_id');
		}
	}
}
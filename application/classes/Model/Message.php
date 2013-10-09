<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Messages
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Message extends ORM {
	/**
	 * A message has many pings
	 */
	protected $_has_many = array(
		'pings' => array(),
		);

	/**
	 * A message belongs to a user
	 */
	protected $_belongs_to = array(
		'user' => array()
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
	protected $_updated_column = array('column' => 'updated', 'format' => 'Y-m-d H:i:s');
	
	public function rules()
	{
		return array(
			'title' => array(
				array('min_length', array(':value', 2)),
				array('max_length', array(':value', 120)),
				array(array($this, 'valid_title'), array(':validation', ':field')),
			),
			'message' => array(
				array('not_empty'),
				array('min_length', array(':value', 2)),
				array('max_length', array(':value', 120)),
			),
			'type' => array(
				array('not_empty'),
				array('in_array', array(':value', array('sms', 'email', 'twitter')) ),
			),
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
			'title' => array(
				array('trim'),
			),
			'message' => array(
				array('trim'),
			),
		);
	}

	/**
	 * Validate Message Against Message Type
	 *
	 * @param array $validation
	 * @param string $field field name
	 * @param [type] [varname] [description]
	 * @return void
	 */
	public function valid_title($validation, $field)
	{
		// Valid Email?
		if ( isset($validation['type']) AND 
			$validation['type'] == 'email' AND ! $validation[$field] )
		{
			$validation->error($field, 'invalid_title');
		}
	}
}
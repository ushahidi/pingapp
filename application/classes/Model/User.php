<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Users
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_User extends Model_Auth_User {
	/**
	 * A user has many tokens and roles
	 * A user has many posts, post_comments, roles and sets 
	 * 
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'roles' => array('through' => 'roles_users'),
		'messages' => array(),
		'people' => array(),
		'groups' => array(),
	);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
	protected $_updated_column = array('column' => 'updated', 'format' => 'Y-m-d H:i:s');

	/**
	 * Rules for the user model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'email' => array(
				array('Valid::email'),
				array(array($this, 'unique'), array(':field', ':value')),
			),
			
			//First name of user
			'first_name' => array(
				array('max_length', array(':value', 150)),
			),
			
			//Last name of user
			'last_name' => array(
				array('max_length', array(':value', 150)),
			),
			
			//username of user
			'username' => array(
				array('min_length', array(':value', 3)),
				array('max_length', array(':value', 255)),
				array(array($this, 'unique'), array(':field', ':value')),
			),
			
			//password of user
			'password' => array(
				array('min_length', array(':value', 7)),
				array('max_length', array(':value', 72)), // Bcrypt max length is 72
				// NOTE: Password should allow ANY character at all. Do not limit to alpha numeric or alpha dash.
			)
		);
			
	}
}

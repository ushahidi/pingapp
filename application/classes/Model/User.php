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
	 * A user has many roles, messages, people, groups and settings
	 * 
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'roles' => array('through' => 'roles_users'),
		'messages' => array(),
		'people' => array(),
		'groups' => array(),
		'settings' => array(),
		'person_statuses' => array(),
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
				array('not_empty'),
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
				array('not_empty'),
				array('min_length', array(':value', 3)),
				array('max_length', array(':value', 255)),
				array(array($this, 'unique'), array(':field', ':value')),
			),
			
			//password of user
			'password' => array(
				array('not_empty'),
				array('min_length', array(':value', 7)),
				array('max_length', array(':value', 72)), // Bcrypt max length is 72
				// NOTE: Password should allow ANY character at all. Do not limit to alpha numeric or alpha dash.
			)
		);
	}

	/**
	 * Create a new user
	 *
	 * Example usage:
	 * ~~~
	 * $user = ORM::factory('User')->create_user($_POST, array(
	 *	'username',
	 *	'password',
	 *	'email',
	 * );
	 * ~~~
	 *
	 * @param array $values
	 * @param array $expected
	 * @throws ORM_Validation_Exception
	 */
	public function create_user($values, $expected)
	{
		// Validation for passwords
		$extra_validation = Model_User::get_password_validation($values)
			->rule('token', 'not_empty')
			->rule('token', 'Security::check')
			->rule('password', 'not_empty');

		return $this->values($values, $expected)->create($extra_validation);
	}

	/**
	 * Password validation for plain passwords.
	 *
	 * @param array $values
	 * @return Validation
	 */
	public static function get_password_validation($values)
	{
		return Validation::factory($values)
			->rule('password', 'min_length', array(':value', 7))
			->rule('password_confirm', 'matches', array(':validation', ':field', 'password'));
	}
}

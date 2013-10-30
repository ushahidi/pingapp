<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * CrowdmapID authorization library. Handles login of users when the system's
 * authentication provider is CrowdmapID
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author      Ushahidi Team <team@ushahidi.com>
 * @package     PingApp
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */
class PingApp_Auth_ORM extends Kohana_Auth_ORM {

	/**
	 * Registers a user account.
	 *
	 * @param array $post 
	 * @return  boolean
	 */
	public function register($post)
	{
		$user = ORM::factory('User');

		try
		{
			$user->create_user($post, array(
				'username',
				'password',
				'email',
				'first_name',
				'last_name',
			));

			$user->add('roles', ORM::factory('Role', array('name' => 'login')));
			$user->add('roles', ORM::factory('Role', array('name' => 'member')));

			// Finish the login
			$this->complete_login($user);
		}
		catch (ORM_Validation_Exception $e)
		{
			throw $e;
			return FALSE;
		}

		return TRUE;
	}

}

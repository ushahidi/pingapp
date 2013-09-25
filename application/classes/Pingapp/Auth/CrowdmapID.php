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
class Pingapp_Auth_CrowdmapID extends Kohana_Auth_ORM {

	/**
	 * Logs a user in.
	 *
	 * @param   string   email
	 * @param   string   password
	 * @param   boolean  enable autologin
	 * @return  boolean
	 */
	protected function _login($email, $password, $remember)
	{
		$crowdmapid_api = CrowdmapID_API::instance();

		// Fallback to local auth if user is in the exemption list
		if (in_array($email, Kohana::$config->load('crowdmapid.auth_exempt')))
			return parent::_login($email, $password, $remember);

		// Check if they provided a username as opposed to an email:
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);
		if( ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$user = ORM::factory('User')
			    ->where('username', '=', $email)
			    ->find();

			if(isset($user->email)) {
				$email = $user->email;
			}
		}

		// Check if the email is registered on CrowdmapID
		if ($crowdmapid_api->is_registered($email))
		{
			// Success! Proceed to sign in into CrowdmapID
			$login = $crowdmapid_api->login($email, $password);

			if ($login AND $login->success)
			{
				// Get the user object that matches the provided email and CrowdmapID
				$user = ORM::factory('User')
				    ->where('email', '=', $email)
				    ->find();

				// User does not exist locally but authenticates via CrowdmapID, create user
				if ( ! $user->loaded())
				{
					// Check if the email is already registered locally
					// If so, this will simply append a riverid
					$user = ORM::factory('User')
					            ->where('email', '=', $email)
					            ->find();

					$user->username = $user->email = $email;
					$user->password = md5(mt_rand() . microtime(true));
					$user->first_name = '';
					$user->last_name = '';
					$user->save();

					try
					{
						// Allow the user be able to login immediately
						$user_roles = ORM::factory('Role')
						    ->where('name', 'IN', array('login', 'member'))
						    ->find_all();

						$role_ids = array();
						foreach ($user_roles as $role)
						{
							$role_ids[] = $role->id;
						}

						if ( ! $user->has('roles', $role_ids))
						{
							$user->add('roles', $role_ids);
						}
					}
					catch (Exception $e)
					{
						// An error has occurred, delete the user
						$user->delete();

						// Log the error
						Kohana::$log->add(Log::ERROR, $e->getMessage());

						return FALSE;
					}
				}

				// User exists locally and authenticates via CrowdmapID so complete the login
				if ($user->has('roles', ORM::factory('Role', array('name' => 'login'))))
				{
					if ($remember === TRUE)
					{
						// Token data
						$data = array(
							'user_id'    => $user->id,
							'expires'    => time() + $this->_config['lifetime'],
							'user_agent' => sha1(Request::$user_agent),
						);

						// Create a new autologin token
						$token = ORM::factory('User_Token')
						            ->values($data)
						            ->create();

						// Set the autologin cookie
						Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
					}

					// Finish the login
					$this->complete_login($user);

					return TRUE;
				}

			}
		}

		return FALSE;
	}

	/**
	 * Registers a user account.
	 *
	 * @param   string   email
	 * @param   string   password
	 * @param   string   username
	 * @param   string   first_name
	 * @param   string   last_name
	 * @return  boolean
	 */
	public function register($email, $password, $username, $first_name = '', $last_name = '')
	{
		$crowdmapid_api = CrowdmapID_API::instance();
		$session = NULL;

		// Check if the email address is already registered.
		$collision = ORM::factory('User')
			->where('email', '=', $email)
			->find();

		if ($collision->loaded()) {
			if( ! $this->_login($email, $password, FALSE)) {
				define('REGISTER_ERROR', 'EMAIL_COLLISION');
				return FALSE;
			}
		}

		// Check if the username is already in use.
		$collision = ORM::factory('User')
			->where('username', '=', $username)
			->find();

		if ($collision->loaded()) {
			define('REGISTER_ERROR', 'USERNAME_COLLISION');
			return FALSE;
		}

		// Fallback to local auth if user is in the exemption list
		if (in_array($email, Kohana::$config->load('crowdmapid.auth_exempt')))
			return parent::_login($email, $password, $remember);

		// Check if the email is registered on CrowdmapID
		if ($crowdmapid_api->is_registered($email))
		{
			// It is. Confirm their supplied password.
			$session = $crowdmapid_api->login($email, $password);

			if (!$session OR !$session->success) {
				// Password provided does not match the one attached to the existing CrowdmapID
				define('REGISTER_ERROR', 'PASSWORD_COLLISION');
				return FALSE;
			}

		} else {
			// It isn't. Attempt to register it.
			$session = $crowdmapid_api->register($email, $password);

			if (!$session OR !$session->success)
			{
				// There was a problem registering the account with CMID.
				if(isset($session->error)) {
					define('REGISTER_ERROR', $session->error);
				} else {
					define('REGISTER_ERROR', 'GENERIC_ERROR');
				}
				return FALSE;
			}

		}

		// Do we have a session, one way or another?
		if($session AND isset($session->user_id) AND isset($session->session_id)) {

			// Yup. Create the local account.
			$user = ORM::factory('User');
			$user->values(array(
				'username'         => $username,
				'password'         => $password,
				'password_confirm' => $password,
				'email'            => $email,
				'first_name'       => $first_name,
				'last_name'        => $last_name
			));

			try
			{
				$user->save();
				$user->add('roles', ORM::factory('Role')->where('name', '=', 'login')->find() );
				$user->add('roles', ORM::factory('Role')->where('name', '=', 'member')->find() );

				// Finish the login
				$this->complete_login($user);

				return TRUE;
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = $e->errors('users');
			}

		}

		return FALSE;
	}

}

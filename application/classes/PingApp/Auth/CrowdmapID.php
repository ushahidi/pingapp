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
class PingApp_Auth_CrowdmapID extends Kohana_Auth_ORM {

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
		if ( ! filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$user = ORM::factory('User')
			    ->where('username', '=', $email)
			    ->find();

			if(isset($user->email))
			{
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
	 * @param array $post post fields
	 * @return  boolean
	 */
	public function register($post)
	{
		$crowdmapid_api = CrowdmapID_API::instance();
		$session = NULL;

		// Check if the email address is already registered.
		$collision = ORM::factory('User')
		    ->where('email', '=', $post['email'])
		    ->find();

		if ($collision->loaded()) {
			if ( ! $this->_login($post['email'], $post['password'], FALSE))
			{
				throw new Kohana_Exception('email address is already registered');
				return FALSE;
			}
		}

		// Check if the username is already in use.
		$collision = ORM::factory('User')
		    ->where('username', '=', $post['username'])
		    ->find();

		if ($collision->loaded())
		{
			throw new Kohana_Exception('username is already registered');
			return FALSE;
		}

		// Fallback to local auth if user is in the exemption list
		if (in_array($post['email'], Kohana::$config->load('crowdmapid.auth_exempt')))
			return parent::_login($post['email'], $post['password']);

		// Check if the email is registered on CrowdmapID
		if ($crowdmapid_api->is_registered($post['email']))
		{
			// It is. Confirm their supplied password.
			$session = $crowdmapid_api->login($post['email'], $post['password']);

			if ( ! $session OR ! $session->success) {
				// Password provided does not match the one attached to the existing CrowdmapID
				throw new Kohana_Exception('invalid username or password');
				return FALSE;
			}

		}
		else
		{
			// It isn't. Attempt to register it.
			$session = $crowdmapid_api->register($post['email'], $post['password']);

			if ( ! $session OR ! $session->success)
			{
				// There was a problem registering the account with CMID.
				if (isset($session->error))
				{
					throw new Kohana_Exception($session->error);
				}
				else
				{
					throw new Kohana_Exception('unknown error');
				}
				return FALSE;
			}

		}

		// Do we have a session, one way or another?
		if ($session AND isset($session->user_id) AND isset($session->session_id))
		{
			$user = ORM::factory('user');			

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
		}
		return TRUE;
	}

}

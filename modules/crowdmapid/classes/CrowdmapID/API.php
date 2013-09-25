<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * CrowdmapID API
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author      Ushahidi Team <team@ushahidi.com>
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */
class CrowdmapID_API {

	/**
	 * RiverID API endpoint
	 *
	 * @var string
	 */
	 protected $api_endpoint = '';
	 protected $api_secret = '';

	/**
	 * Singleton instance for this class
	 * @var RiverID_API
	 */
	private static $_singleton;

	/**
	 * Creates a singleton instance for the RiverID_API class
	 *
	 * @return RiverID_API
	 */
	public static function instance()
	{
		if ( ! self::$_singleton)
		{
			self::$_singleton = new CrowdmapID_API(Kohana::$config->load('crowdmapid.api_url'),
				Kohana::$config->load('crowdmapid.api_secret'));
		}

		return self::$_singleton;
	}

	/**
	 * Creates an instance of this class
	 * A private function so that only one instance is created
	 */
	private function __construct($api_endpoint, $api_secret)
	{
		$this->api_endpoint = $api_endpoint;
		$this->api_secret = $api_secret;
	}

	/**
	 * Checks if an email is registered.
	 *
	 * @param   string   email
	 * @return  boolean
	 */
	public function is_registered($email)
	{
		if ($exists = $this->__api_call('GET', "/user/{$email}")) {
			if ($exists = $this->__reduce_response($exists, 'user')) {
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Return a user profile.
	 *
	 * @param   string   $user_id  An email address or 128 character user hash.
	 * @param   array    $params   Additional parameters to pass, such as a session token.
	 * @return  array
	 *
	 * @access public
	 */
	public function get_profile($user_id, $params = array()) {

		if ($user = $this->__api_call('GET', "/user/{$user_id}", $params)) {
			if ($user = $this->__reduce_response($user, 'user')) {
				return $user;
			}
		}

		return FALSE;

	}

	/**
	 * Logs in a user via RiverID
	 *
	 * @param   string   $email
	 * @param   string   $password
	 * @return  array
	 */
	public function login($email, $password, $otp = NULL)
	{
		$email    = filter_var($email, FILTER_SANITIZE_EMAIL);
		$password = filter_var($password, FILTER_SANITIZE_STRING);
		$otp      = ($otp ? filter_var($otp, FILTER_SANITIZE_STRING) : NULL);

		if ($session = $this->__api_call('GET', "/user/{$email}/password", array('password' => $password, 'otp' => $otp))) {

			if ($session AND isset($session->success) AND $session->success === TRUE) {
				return (object)array(
					'success'     => TRUE,
					'user_id'     => $session->user_id,
					'session_id'  => $session->session_id
				);
			}

			if ($session AND isset($session->error)) {
				return (object)array(
					'success'   => FALSE,
					'error'     => $session->error
				);
			}

		}

		return (object)array(
			'success'  => FALSE,
			'error'    => __('Unknown error')
		);
	}

	/**
	 * Create a CMID using an email/password pair.
	 *
	 * @param   string  $email     An email address of a registered user.
	 * @param   string  $password  The password of a registered user.
	 * @return  array
	 *
	 * @access public
	 */
	public function register($email, $password) {
		$email     = filter_var($email, FILTER_SANITIZE_EMAIL);
		$password  = urlencode(filter_var($password, FILTER_SANITIZE_STRING));

		if ($session = $this->__api_call('POST', "/user", array('email' => $email, 'password' => $password))) {

			if ($session AND isset($session->success) AND $session->success === TRUE) {
				return (object)array(
					'success'     => TRUE,
					'user_id'     => $session->user_id,
					'session_id'  => $session->session_id
				);
			}

			if ($session AND isset($session->error)) {
				return (object)array(
					'success'   => FALSE,
					'error'     => $session->error
				);
			}

		}

		return (object)array(
			'success'  => FALSE,
			'error'    => __('Unknown error')
		);

	}

	/**
	 * Returns all the email addresses associated with a user account.
	 *
	 * @param   string   $user_id     A 128 character user hash.
	 * @param   string   $session_id  A 64 character session hash.
	 * @return  object   API response.
	 *
	 * @access public
	 */
	public function get_emails($user_id, $session_id) {

		if ($emails = $this->__api_call('GET', "/user/{$user_id}/emails", array('user_id' => $user_id, 'session_id' => $session_id))) {
			if(isset($emails->emails)) {
				return $emails->emails;
			}
		}

		return (object)array();

	}

	/**
	 * Send the user a password recovery token in their email.
	 *
	 * @param   string   email
	 * @param   array    params
	 * @return  object
	 *
	 * @access public
	 */
	public function recover_password($email, $params = array()) {
		return $this->__api_call('POST', "/user/{$email}/recover/", $params);
	}

	/**
	 * Validate a password recovery token from email.
	 *
	 * @param   string   email
	 * @param   string   token
	 * @param   array    params
	 * @return  object
	 *
	 * @access public
	 */
	public function confirm_recover_password($email, $token, $params = array()) {
		$params['token'] = $token;
		return $this->__api_call('GET', "/user/{$email}/recover/", $params);
	}

	/**
	 * Update the user's password.
	 *
	 * @param   string   $user_id     A 128 character user hash.
	 * @param   string   $session_id  A 64 character session hash.
	 * @param   string   $password    A urlencoded representation of the desired password.
	 * @return  object   API response.
	 *
	 * @access public
	 */
	public function change_password($user_id, $session_id, $password) {
		$params = array(
			'user_id'    => $user_id,
			'session_id' => $session_id,
			'password'   => $password
		);

		return $this->__api_call('PUT', "/user/{$user_id}/password/", $params);
	}

	/**
	 * Return a storage value.
	 *
	 * @param   string   $user_id     A 128 character user hash.
	 * @param   string   $session_id  A 64 character session hash.
	 * @param   string   $key         The storage object's key.
	 * @param   string   $default     The default value to return if the store doesn't exist.
	 * @return  object   API response.
	 *
	 * @access public
	 */
	public function storage_get($user_id, $session_id, $key, $default = FALSE) {
		$key = trim($key);

		if ($store = $this->__api_call('GET', "/user/{$user_id}/store/{$key}", array('user_id' => $user_id, 'session_id' => $session_id))) {
			if(isset($store->response) AND strlen($store->response)) {
				return $store->response;
			}
		}

		return $default;
	}

	/**
	 * Save or update a storage key/value pair.
	 *
	 * @param   string   $user_id     A 128 character user hash.
	 * @param   string   $session_id  A 64 character session hash.
	 * @param   string   $key         The storage pair's key.
	 * @param   string   $value       The new value for the pair.
	 * @return  object   API response.
	 *
	 * @access public
	 */
	public function storage_put($user_id, $session_id, $key, $value) {
		$key = trim($key);

		if ($store = $this->__api_call('POST', "/user/{$user_id}/store/{$key}", array('user_id' => $user_id, 'session_id' => $session_id, 'value' => trim($value)))) {
			if(isset($store->success)) {
				return $store->success;
			}
		}

		return FALSE;
	}

	/**
	 * Delete a storage key/value pair.
	 *
	 * @param   string   $user_id     A 128 character user hash.
	 * @param   string   $session_id  A 64 character session hash.
	 * @param   string   $key         The storage pair's key.
	 * @return  object   API response.
	 *
	 * @access public
	 */
	public function storage_delete($user_id, $session_id, $key) {
		$key = trim($key);

		if ($store = $this->__api_call('DELETE', "/user/{$user_id}/store/{$key}", array('user_id' => $user_id, 'session_id' => $session_id))) {
			if(isset($store->success)) {
				return $store->success;
			}
		}

		return FALSE;
	}

	/**
	 * Update user avatar.
	 *
	 * @param   string   $user_id     A 128 character user hash.
	 * @param   string   $session_id  A 64 character session hash.
	 * @param   string   $avatar      The URI pointing to the user's avatar.
	 * @return  object   API response.
	 *
	 * @access public
	 */
	public function avatar_put($user_id, $session_id, $avatar_uri) {
		if ($avatar = $this->__api_call('PUT', "/user/{$user_id}/avatar/", array('user_id' => $user_id, 'session_id' => $session_id, 'avatar' => $avatar_uri))) {
			if(isset($avatar->success)) {
				return $avatar->success;
			}
		}

		return FALSE;
	}

	/**
	 * Delete user avatar.
	 *
	 * @param   string   $user_id     A 128 character user hash.
	 * @param   string   $session_id  A 64 character session hash.
	 * @return  object   API response.
	 *
	 * @access public
	 */
	public function avatar_delete($user_id, $session_id) {
		if ($response = $this->__api_call('DELETE', "/user/{$user_id}/avatar/", array('user_id' => $user_id, 'session_id' => $session_id))) {
			if(isset($response->success)) {
				return $response->success;
			}
		}

		return FALSE;
	}

	/**
	 * Returns basic information about attached Yubikey devices to user accounts.
	 *
	 * @param   string   $user_id     A 128 character user hash.
	 * @param   string   $session_id  A 64 character session hash.
	 * @return  object   API response.
	 *
	 * @access public
	 */
	public function yubikey_status($user_id, $session_id) {
		return $this->__api_call('GET', "/user/{$user_id}/security/yubikey", array( 'user_id' => $user_id, 'session_id' => $session_id ));
	}

	/**
	 * Attach a Yubikey device to the user account.
	 *
	 * @param   string   $user_id     A 128 character user hash.
	 * @param   string   $session_id  A 64 character session hash.
	 * @param   string   $otp         A opt (one-time password) hash from a Yubikey device.
	 * @return  object   API response.
	 *
	 * @access public
	 */
	public function yubikey_pair($user_id, $session_id, $otp) {
		return $this->__api_call('POST', "/user/{$user_id}/security/yubikey", array( 'otp' => $otp, 'user_id' => $user_id, 'session_id' => $session_id ));
	}

	/**
	 * Remove an attached Yubikey device from the user account.
	 *
	 * @param   string   $user_id     A 128 character user hash.
	 * @param   string   $session_id  A 64 character session hash.
	 * @return  object   API response.
	 *
	 * @access public
	 */
	public function yubikey_delete($user_id, $session_id) {
		return $this->__api_call('DELETE', "/user/{$user_id}/security/yubikey", array( 'user_id' => $user_id, 'session_id' => $session_id ));
	}

	/**
	 * Returns basic information about attached Google Authenticators to user accounts.
	 *
	 * @param   string   $user_id     A 128 character user hash.
	 * @param   string   $session_id  A 64 character session hash.
	 * @return  object   API response.
	 *
	 * @access public
	 */
	public function googauth_status($user_id, $session_id) {
		return $this->__api_call('GET', "/user/{$user_id}/security/googleauth", array( 'user_id' => $user_id, 'session_id' => $session_id ));
	}

	/**
	 * Attach a Google Authenticator to the user account.
	 *
	 * @param   string   $user_id     A 128 character user hash.
	 * @param   string   $session_id  A 64 character session hash.
	 * @param   string   $otp         A opt (one-time password) hash from a Yubikey device.
	 * @return  object   API response.
	 *
	 * @access public
	 */
	public function googauth_pair($user_id, $session_id, $otp) {
		return $this->__api_call('POST', "/user/{$user_id}/security/googleauth", array( 'otp' => $otp, 'user_id' => $user_id, 'session_id' => $session_id ));
	}

	/**
	 * Remove an attached Google Authenticator from the user account.
	 *
	 * @param   string   $user_id     A 128 character user hash.
	 * @param   string   $session_id  A 64 character session hash.
	 * @return  object   API response.
	 *
	 * @access public
	 */
	public function googauth_delete($user_id, $session_id) {
		return $this->__api_call('DELETE', "/user/{$user_id}/security/googleauth", array( 'user_id' => $user_id, 'session_id' => $session_id ));
	}

	/**
	 * Retrieve basic information about the API endpoint.
	 *
	 * @return  object  API response.
	 *
	 * @access public
	 */
	public function about() {
		return $this->__api_call('GET', '/about');
	}

	/**
	 * Determine how many hits this API key has remaining.
	 *
	 * @return  object  API response.
	 *
	 * @access public
	 */
	public function limit() {
		return $this->__api_call('GET', '/limit');
	}

	/**
	 * Send HTTP request to the api endpoint
	 *
	 * @param   string   url      Endpoint path to target with request.
	 * @param   string   method   HTTP request method (i.e. GET, POST, DELETE)
	 * @param   array    params   Parameters to supply the API for this request.
	 * @return  mixed    The response or false in case of failure
	 */
	private function __api_call($method = 'GET', $url ='/about', $params = array()) {

		$api = curl_init();

		if ($api) {

			$params = array_merge(array(
				'api_secret'  => $this->api_secret
			), $params);

			$url .= '?';

			switch($method)
			{
				case 'POST':
					curl_setopt($api, CURLOPT_POST, TRUE);
					curl_setopt($api, CURLOPT_POSTFIELDS, $params);
					break;

				case 'PUT':
					curl_setopt($api, CURLOPT_CUSTOMREQUEST, 'PUT');
					break;

				case 'DELETE':
					curl_setopt($api, CURLOPT_CUSTOMREQUEST, 'DELETE');
					break;
			}

			if ($method !== 'POST' AND count($params)) {
				foreach($params as $p => $v) {
					$url .= $p . '=' . urlencode($v) . '&';
				}
			}

			$url = rtrim($this->api_endpoint, '/') . '/v2' . rtrim($url, '?&');

			curl_setopt_array($api, array(
				CURLOPT_URL            => $url,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_SSL_VERIFYHOST => FALSE,
				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_FOLLOWLOCATION => TRUE,
				CURLOPT_FAILONERROR    => TRUE,
				CURLOPT_USERAGENT      => (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/536.5 (KHTML, like Gecko) PingApp Chrome/19.0.1084.9 Safari/536.5'),
				CURLOPT_MAXREDIRS      => 12,
				CURLOPT_TIMEOUT        => 5
				));

			if ($raw = curl_exec($api)) {
				@curl_close($api);

				if ($resp = json_decode($raw)) {
					return $resp;
				}
			}

			/*
			echo $url;
			var_dump($raw);
			exit;
			*/

			Kohana::$log->add(Log::ERROR, "RiverID api call failed. :error", array('error' => curl_error($api)));

			return false;
		}

	}

	private function __reduce_response($haystack, $needle) {
		if (isset($haystack->$needle))
			return $haystack->$needle;

		return false;
	}

}

?>

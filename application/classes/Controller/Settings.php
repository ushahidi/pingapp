<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Settings Controller
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Controller_Settings extends Controller_PingApp {
	
	/**
	 * Admin Only Access
	 */
	public $auth_required = array('admin');

	private $_post;
	private $_errors;
	private $_done;

	/**
	 * Settings Main
	 * 
	 * @return void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('pages/settings/index');
	}

	/**
	 * Edit Email Settings
	 * 
	 * @return void
	 */
	public function action_email()
	{
		$this->_action('email');
	}

	/**
	 * Pings
	 * 
	 * @return void
	 */
	public function action_pings()
	{
		$this->_action('pings');
	}

	/**
	 * Customize Messages
	 * 
	 * @return void
	 */
	public function action_customize()
	{
		$this->_action('customize');
	}

	/**
	 * Edit TOS Settings
	 * 
	 * @return void
	 */
	public function action_tos()
	{
		$this->_action('tos');
	}

	/**
	 * Edit SMS Settings
	 * 
	 * @return void
	 */
	public function action_sms()
	{
		$this->template->content = View::factory('pages/settings/sms')
			->bind('settings', $settings)
			->bind('post', $this->_post)
			->bind('errors', $this->_errors)
			->bind('plugins', $plugins)
			->bind('done', $this->_done);

		$this->_save();

		$config = Kohana::$config->load('_plugins');

		$plugins = array();
		foreach ($config as $key => $plugin)
		{
			if ( isset($plugin['services']['sms']) AND $plugin['services']['sms'] )
			{
				$plugins[$key] = $plugin;
			}
		}
	}

	/**
	 * Get Settings Page
	 *
	 * @param string $action
	 * @return void
	 */
	private function _action($action = NULL)
	{
		$this->template->content = View::factory('pages/settings/'.$action)
			->bind('settings', $settings)
			->bind('post', $this->_post)
			->bind('errors', $this->_errors)
			->bind('done', $this->_done);

		$this->_save();
	}

	/**
	 * Save Settings
	 * 
	 * @return void
	 */
	private function _save()
	{
		if ( ! empty($_POST) )
		{
			$this->_post = $_POST;
			// - Settings Model Validation
			foreach ($this->_post['settings'] as $key => $value)
			{
				PingApp_Settings::set($key, $value);
			}

			// Redirect to prevent repost
			HTTP::redirect(Request::current()->uri().'?done');
		}
		else
		{
			$this->_post = array(
				'settings' => array()
			);
			$settings = ORM::factory('Setting')
				->where('user_id', '=', 0)
				->find_all();

			foreach ($settings as $setting)
			{
				$this->_post['settings'][$setting->key] = $setting->value;
			}

			$this->_done = (isset($_GET['done'])) ? TRUE : FALSE;
		}
	}
}
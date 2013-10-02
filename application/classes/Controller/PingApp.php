<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * PingApp Base Controller
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Controller_PingApp extends Controller_Template {
	
	/**
	 * @var boolean Whether the template file should be rendered automatically.
	 */
	public $auto_render = TRUE;
	
	/**
	 * @var string Filename of the template file.
	 */
	public $template = 'common/layout';

	/**
	 * @var string Context (user | manager | admin)
	 */
	public $context;	
	
	/**
	 * Controls access for the controller and sub controllers, if not set to FALSE we will only allow user roles specified
	 */
	public $auth_required = array('member', 'admin');

	/** Controls access for separate actions
	 * 
	 * Examples:
	 * 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
	 * 'memberpanel' => array('login', 'manager') will only allow users with the roles login and manager
	 */
	public $secure_actions = FALSE;	

	/**
	 * Logged In User
	 */
	public $user = NULL;

	/**
	 * This Session
	 */
	protected $session;

	/**
	 * Cache instance
	 * @var Cache
	 */
	protected $cache = NULL;
	
	public function before()
	{
		// Get rid of invalid sessions which cause exceptions, which may happen
		// 1) when you make errors in your code.
		// 2) when the session expires!
		try
		{
			$this->session = Session::instance();
		}
		catch (ErrorException $e)
		{
			session_destroy();
		}
		// Execute parent::before first
		parent::before();
		// Open session
		$this->session = Session::instance();

		//if we're not logged in, but auth type is orm. gives us chance to auto login
		$supports_auto_login = new ReflectionClass(get_class(Auth::instance()));
		$supports_auto_login = $supports_auto_login->hasMethod('auto_login');
		if(!Auth::instance()->logged_in() && $supports_auto_login)
		{
			Auth::instance()->auto_login();
		}

		// Check user auth and role
		$action_name = Request::current()->action();

		// Okay User is Logged In -- On to ACL
		if (Auth::instance()->logged_in())
		{
			$this->user = Auth::instance()->get_user();

			// Is Access Restricted?
			if ($this->auth_required !== FALSE)
			{
				$access = FALSE;
				// Verify That User Has Correct Role
				foreach ($this->auth_required as $role)
				{
					if ($this->user->has('roles', ORM::factory('Role')->where('name', '=', $role)->find() ))
					{
						// Make Sure this Role has Access to This Action
						if ( is_array($this->secure_actions) AND in_array($role, $this->secure_actions[$action_name]) )
						{
							$access = TRUE;
							$this->role = $role;
							break;
						}
						else
						{
							$access = TRUE;
							$this->role = $role;
							break;
						}
					}
				}

				// user is logged in but not on the secure_actions list
				if ( ! $access)
				{
					$this->_login_required();
				}
			}
		}
		else
		{
			$this->_login_required();
		}

		if ($this->auto_render)
		{
			$this->template->header = View::factory('common/header')
				->bind('nav', $nav);
			$this->template->content = '';
			$this->template->footer = View::factory('common/footer');
			$this->template->footer->js = '';

			$nav = View::factory('common/nav')
				->bind('user', $this->user)
				->bind('role', $role);

			if ($this->user->has('roles', ORM::factory('Role')->where('name', '=', 'admin')->find() ))
			{
				$role = 'admin';
			}
			else
			{
				$role = 'member';
			}
		}
	}

	/**
	 * Called from before() when the user is not logged in but they should.
	 *
	 * Override this in your own Controller / Controller_App.
	 */
	private function _login_required()
	{
		// Sign out the user
		Auth::instance()->logout();

		HTTP::redirect('login');
	}
}

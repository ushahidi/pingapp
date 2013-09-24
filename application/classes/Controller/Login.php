<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Login Controller
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Controller_Login extends Controller_Template {
	
	/**
	 * @var	 bool auto render
	 */
	public $auto_render = TRUE;
	
	/**
	 * @var	 string	 page template
	 */
	public $template = 'pages/login';

	public function before()
	{
		// Execute parent::before first
		parent::before();
	}
	
	/**
	 * @return	void
	 */	
	public function action_index()
	{
		// check, has the form been submitted, if so, setup validation
		if ($_POST AND
	 		isset($_POST['username'], $_POST['password']))
		{
			// Get errors for display in view
			$validation = Validation::factory($_POST)
				->rule('username', 'not_empty')
				->rule('password', 'not_empty')
				->rule('token', 'not_empty')
				->rule('token', 'Security::check');

			// Check Auth if the post data validates using the rules setup in the user model
			if ( $validation->check() AND Auth::instance()->login(
					$_POST['username'],
					$_POST['password']) )
			{
				HTTP::redirect('dashboard');
			}
			else
			{
				$this->template->set('username', $_POST['username']);
				if ($validation->check())
				{
					$validation->error('password', 'invalid');
				}
				$this->template->set('errors', $validation->errors('login'));
			}
		}
	}
	
	/**
	 * Logut action
	 * @return	void
	 */	
	public function action_logout()
	{
		// Sign out the user
		Auth::instance()->logout();
		
		HTTP::redirect('/');
	}
}
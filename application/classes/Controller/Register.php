<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Registration Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Controller_Register extends Controller_Template {

	/**
	 * @var	 bool auto render
	 */
	public $auto_render = TRUE;

	/**
	 * @var	 string	 page template
	 */
	public $template = 'pages/register';

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
	 		isset($_POST['username'], $_POST['password'], $_POST['email'], $_POST['first_name'], $_POST['last_name']))
		{
			// Get errors for display in view
			$validation = Validation::factory($_POST)
				->rule('username', 'not_empty')
				->rule('password', 'not_empty')
				->rule('email', 'not_empty')
				->rule('first_name', 'not_empty')
				->rule('last_name', 'not_empty')
				->rule('token', 'not_empty')
				->rule('token', 'Security::check');

			// Check Auth if the post data validates using the rules setup in the user model
			if ( $validation->check() AND Auth::instance()->register(
					trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)),
					trim(filter_var($_POST['password'], FILTER_SANITIZE_EMAIL)),
					trim(filter_var($_POST['username'], FILTER_SANITIZE_EMAIL)),
					trim(filter_var($_POST['first_name'], FILTER_SANITIZE_EMAIL)),
					trim(filter_var($_POST['last_name'], FILTER_SANITIZE_EMAIL))))
			{
				HTTP::redirect('dashboard');
			}
			else
			{
				$this->template->set('username', $_POST['username']);
				$this->template->set('email', $_POST['email']);
				$this->template->set('first_name', $_POST['first_name']);
				$this->template->set('last_name', $_POST['last_name']);

				if(defined('REGISTER_ERROR')) {
					//$validation->error('crowdmapid', strtolower(REGISTER_ERROR));
					//$this->template->set('errors', $validation->errors('register')); // TODO Having issues getting message lookups to work.

					$this->template->set('errors', array(REGISTER_ERROR));
				} else {
					if ($validation->check()) {
						$validation->error('password', 'invalid');
					}
					$this->template->set('errors', $validation->errors('login'));
				}
			}
		}
	}

}

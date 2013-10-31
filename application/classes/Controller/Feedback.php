<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Feedback Controller
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Controller_Feedback extends Controller_PingApp {
	
	/**
	 * Submit Feedback
	 * 
	 * @return void
	 */
	public function action_index()
	{
		$feedback_email = PingApp_Settings::get('feedback_email');

		if ( ! $feedback_email)
		{
			HTTP::redirect('dashboard');
		}

		$this->template->content = View::factory('pages/feedback')
			->bind('post', $post)
			->bind('errors', $errors)
			->bind('done', $done);

		if ( ! empty($_POST) )
		{
			$post = Validation::factory($_POST)
				->rule('name', 'not_empty')
				->rule('email', 'not_empty')
				->rule('email', 'email')
				->rule('message', 'not_empty')
				->rule('token', 'not_empty')
				->rule('token', 'Security::check');

			if ($post->check())
			{
				$this->_config_email();

				$email = Email::factory('PingApp Feedback', $post['message'])
					->to($feedback_email)
					->from($post['email'], $post['name'])
					->reply_to($post['email'])
					->send();

				// Redirect to prevent repost
				HTTP::redirect('feedback?done');
			}
			else
			{
				$errors = $post->errors('feedback');
			}
		}
		else
		{
			$done = (isset($_GET['done'])) ? TRUE : FALSE;
		}
	}

	/**
	 * Use Systems Configured Outgoing Email Provider
	 * 
	 * @return void
	 */
	private function _config_email()
	{
		$driver = PingApp_Settings::get('email_outgoing_type');
		$options = array(
			'hostname' => PingApp_Settings::get('email_outgoing_host'),
			'port' => PingApp_Settings::get('email_outgoing_port'),
			'encryption' => (PingApp_Settings::get('email_outgoing_security') != 'none') 
				? PingApp_Settings::get('email_outgoing_security') : '',
			'username' => PingApp_Settings::get('email_outgoing_username'),
			'password' => PingApp_Settings::get('email_outgoing_password')
			);

		$config = Kohana::$config->load('email');
		$config->set('driver', $driver);
		$config->set('options', $options);
	}
}
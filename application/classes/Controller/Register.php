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
		$this->template->bind('post', $post);

		// check, has the form been submitted, if so, setup validation
		if ($_POST AND
	 		isset($_POST['username'], $_POST['password'], $_POST['email'], $_POST['first_name'], $_POST['last_name']))
		{
			$post = $_POST;

			try
			{
				$post['password_confirm'] = $post['password'];
				Auth::instance()->register($post);

				HTTP::redirect('dashboard');
			}
			catch (ORM_Validation_Exception $e)
			{
				$this->template->set( 'errors', Arr::flatten($e->errors('register')) );
			}
			catch (Kohana_Exception $e)
			{
				$this->template->set( 'errors', array($e->getMessage()) );
			}
		}
	}

}

<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Info Controller
 * Render Terms of Service, Privacy Policies, Refund Policies here
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Controller_Info extends Controller_PingApp {

	private $_title;
	private $_content;

	public $auth_required = FALSE;

	public function before()
	{
		parent::before();

		$this->template->content = View::factory('pages/info/view')
			->bind('title', $this->_title)
			->bind('content', $this->_content);
	}

	/**
	 * List People
	 * 
	 * @return void
	 */
	public function action_tos()
	{
		$this->_title = 'Terms of Service';
		$this->_content = nl2br(PingApp_Settings::get('tos'));
	}

	/**
	 * List People
	 * 
	 * @return void
	 */
	public function action_privacy()
	{
		$this->_title = 'Privacy Policy';
		$this->_content = nl2br(PingApp_Settings::get('privacy'));
	}
}

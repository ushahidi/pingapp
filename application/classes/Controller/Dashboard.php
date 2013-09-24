<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Dashboard Controller
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Controller_Dashboard extends Controller_PingApp {
	
	/**
	 * Dashboard
	 * 
	 * @return void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('pages/dashboard');
	}
}
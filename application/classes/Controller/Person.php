<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Person Controller
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Controller_Person extends Controller_PingApp {
	
	/**
	 * Add/Edit Person
	 * 
	 * @return void
	 */
	public function action_edit()
	{
		$this->template->content = View::factory('pages/person/edit');
	}

	/**
	 * View Person
	 * 
	 * @return void
	 */
	public function action_view()
	{
		$this->template->content = View::factory('pages/person/view')
			->bind('person', $person);

		$person_id = $this->request->param('id', 0);

		$person = ORM::factory('Person', $person_id);

		if ( ! $person->loaded() )
		{
			HTTP::redirect('dashboard');
		}
	}
}
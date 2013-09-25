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
		$this->template->footer->js = View::factory('pages/js/dashboard');
	}

	/**
	 * Use datatables to generate dashboard list
	 */
	public function action_ajax_list()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		// Data table columns
		$columns = array('first_name', 'status', 'pings', 'last_name');

		$pings = DB::select('person_id', array(DB::expr('COUNT("id")'), 'pings'))
			->from('pings')
			->group_by('person_id');

		$query = ORM::factory('Person')
			->select(array(DB::expr('CONCAT(person.first_name, " ", person.last_name)'), 'name'))
			->select('pings.pings')
			->join(array($pings, 'pings'), 'LEFT')->on('person.id', '=', 'pings.person_id')
			->where('user_id', '=', $this->user->id);

		$query2 = clone $query;

		// Searching & Filtering
		if (  isset( $_GET['sSearch'] ) AND $_GET['sSearch'] != "" )
		{
			$query->where_open();
			for ( $i=0 ; $i < count($columns) ; $i++ )
			{
				$query->or_where($columns[$i], 'LIKE', '%'.$_GET['sSearch'].'%');
			}
			$query->where_close();
		}

		// Paging
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$query->offset($_GET['iDisplayStart']);
			$query->limit($_GET['iDisplayLength']);
		}

		// Ordering
		if ( isset( $_GET['iSortCol_0'] ) AND $_GET['iSortCol_0'] != 0 )
		{
			$query->order_by($columns[$_GET['iSortCol_0']], $_GET['sSortDir_0']);
		}

		$people = $query->find_all()->as_array();

		//Output
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => count($people),
			"iTotalDisplayRecords" => $query2->count_all(),
			"aaData" => array()
		);

		foreach ($people as $person)
		{
			$row = array(
				0 => '<a href="/person/view/'.$person->id.'"><strong>'.strtoupper($person->name).'</strong></a>',
				1 => '<span class="radius secondary label">'.strtoupper($person->status).'</status>',
				2 => '<span class="radius secondary label">'.(int) $person->pings.'</span>',
				);

			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}
}
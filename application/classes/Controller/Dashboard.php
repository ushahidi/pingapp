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
	 * Use datatables to generate dashboard activity list
	 */
	public function action_ajax_list()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		// Data table columns
		$columns = array('name');

		$pings = DB::select('cp.person_id', 'p.name', 'p.status', 'pings.type', 'c.contact', array('pings.created', 'created_on'), array(DB::expr('"ping"'), 'action'))
			->from('pings')
			->join(array('contacts', 'c'), 'INNER')
				->on('pings.contact_id', '=', 'c.id')
			->join(array('contacts_people', 'cp'), 'INNER')
				->on('c.id', '=', 'cp.contact_id')
			->join(array('people', 'p'), 'INNER')
				->on('cp.person_id', '=', 'p.id')
			->where('p.user_id', '=', $this->user->id)
			->order_by('created_on', 'DESC');

		// Paging
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$pings->offset($_GET['iDisplayStart']);
			$pings->limit($_GET['iDisplayLength']);
		}

		// Union
		$query = DB::select('cp.person_id', 'p.name', 'p.status', 'pongs.type', 'c.contact', array('pongs.created', 'created_on'), array(DB::expr('"pong"'), 'action'))
			->union($pings)
			->from('pongs')
			->join(array('contacts', 'c'), 'INNER')
				->on('pongs.contact_id', '=', 'c.id')
			->join(array('contacts_people', 'cp'), 'INNER')
				->on('c.id', '=', 'cp.contact_id')
			->join(array('people', 'p'), 'INNER')
				->on('cp.person_id', '=', 'p.id');

		$query2 = clone $query;

		$items = $query->execute();

		//Output
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => count($items),
			"iTotalDisplayRecords" => count($query2->execute()),
			"aaData" => array()
		);

		foreach ($items as $item)
		{
			$pong_label = ($item['action'] == 'pong') ? '' : 'secondary';
			$row = array(
				0 => '<a href="/people/view/'.$item['person_id'].'"><strong>'.strtoupper($item['name']).'</strong></a>',
				1 => strtoupper($item['contact']),
				2 => '<span class="radius label secondary">'.strtoupper($item['type']).'</status>',
				3 => '<span class="radius label '.$pong_label.'">'.strtoupper($item['action']).'</status>',
				4 => '<span class="radius secondary label">'.date('Y-m-d g:i a', strtotime($item['created_on'])).'</span>',
				);

			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}
}
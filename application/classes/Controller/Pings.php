<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Pings Controller
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Controller_Pings extends Controller_PingApp {
	
	/**
	 * Pings
	 * 
	 * @return void
	 */
	public function action_index()
	{
		
	}

	/**
	 * Use datatables to generate pings list
	 */
	public function action_ajax_list()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		// Data table columns
		$columns = array('contact', 'message', 'ping.created');

		$query = ORM::factory('Ping')
			->select('c.contact')
			->select('m.message')
			->join(array('contacts', 'c'), 'INNER')
				->on('ping.contact_id', '=', 'c.id')
			->join(array('contacts_people', 'cp'), 'INNER')
				->on('c.id', '=', 'cp.contact_id')
			->join(array('people', 'p'), 'INNER')
				->on('cp.person_id', '=', 'p.id')
			->join(array('messages', 'm'), 'INNER')
				->on('ping.message_id', '=', 'm.id')
			->where('p.user_id', '=', $this->user->id)
			->where('m.user_id', '=', $this->user->id); // Ensure I can only view pings I sent

		if ( isset( $_GET['person_id'] ) AND $_GET['person_id'] != 0 )
		{
			$query->where('p.id', '=', (int) $_GET['person_id']);
		}

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

		$pings = $query->find_all()->as_array();

		//Output
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => count($pings),
			"iTotalDisplayRecords" => $query2->count_all(),
			"aaData" => array()
		);

		foreach ($pings as $ping)
		{
			$row = array(
				0 => '<strong>'.strtoupper($ping->contact).'</strong>',
				1 => '<a href="#" data-reveal-id="ping-'.$ping->id.'">'.Text::limit_chars($ping->message, 30, '...').'</a><div id="ping-'.$ping->id.'" class="reveal-modal">'.$ping->message.'</div>',
				2 => date('Y-m-d g:i a', strtotime($ping->created)),
				);

			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}
}
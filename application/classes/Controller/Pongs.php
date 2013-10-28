<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Pongs Controller
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Controller_Pongs extends Controller_PingApp {
	
	/**
	 * Pongs
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
		$columns = array('contact', 'content', 'pong.created');

		$query = ORM::factory('Pong')
			->select('c.contact')
			->join(array('contacts', 'c'), 'INNER')
				->on('pong.contact_id', '=', 'c.id')
			->join(array('contacts_people', 'cp'), 'INNER')
				->on('c.id', '=', 'cp.contact_id')
			->join(array('people', 'p'), 'INNER')
				->on('cp.person_id', '=', 'p.id')
			->where('p.user_id', '=', $this->user->id);

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

		$pongs = $query->find_all()->as_array();

		//Output
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => count($pongs),
			"iTotalDisplayRecords" => $query2->count_all(),
			"aaData" => array()
		);

		foreach ($pongs as $pong)
		{
			$row = array(
				0 => '<strong>'.strtoupper($pong->contact).'</strong>',
				1 => $pong->content,
				1 => '<a href="#" data-reveal-id="pong-'.$pong->id.'">'.Text::limit_chars($pong->content, 30, '...').'</a><div id="pong-'.$pong->id.'" class="reveal-modal">'.$pong->content.'</div>',
				2 => date('Y-m-d g:i a', strtotime($pong->created)),
				);

			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}
}
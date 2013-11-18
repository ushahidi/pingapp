<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Users Controller
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Controller_Users extends Controller_PingApp {
	
	/**
	 * Admin Only Access
	 */
	public $auth_required = array('admin');

	/**
	 * List All Users
	 * 
	 * @return void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('pages/users/index');
		$this->template->footer->js = View::factory('pages/users/js/index');
	}

	/**
	 * View a Specific User
	 * 
	 * @return void
	 */
	public function action_view()
	{
		$this->template->content = View::factory('pages/users/view')
			->bind('user', $user);
		$this->template->footer->js = View::factory('pages/users/js/view')
			->bind('user', $user);
		
		$user_id = $this->request->param('id', 0);
		$user = ORM::factory('User', $user_id);

		if ( ! $user->loaded() )
		{
			HTTP::redirect('users');
		}
	}

	/**
	 * Use datatables to generate users list
	 */
	public function action_ajax_list()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		// Data table columns
		$columns = array(DB::expr('CONCAT(user.first_name, " ", user.last_name)'), 'email', 'username', 'messages', 'user.created');

		$messages = DB::select('user_id', array(DB::expr('COUNT(id)'), 'messages'))
			->from('messages')
			->group_by('user_id');

		$query = ORM::factory('User')
			->select(array(DB::expr('CONCAT(user.first_name, " ", user.last_name)'), 'name'))
		    ->select('messages.messages')
		    ->join(array($messages, 'messages'), 'LEFT')
		    	->on('user.id', '=', 'messages.user_id');

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
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$query->order_by($columns[$_GET['iSortCol_0']], $_GET['sSortDir_0']);
		}

		$users = $query->find_all()->as_array();

		//Output
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => count($users),
			"iTotalDisplayRecords" => $query2->count_all(),
			"aaData" => array()
		);

		foreach ($users as $user)
		{
			$name = (trim($user->name)) ? $user->name : '**no name**';
			$row = array(
				0 => '<a href="/users/view/'.$user->id.'"><strong>'.$name.'</strong></a>',
				1 => $user->email,
				2 => $user->username,
				3 => '<span class="radius secondary label">'.(int) $user->messages.'</span>',
				4 => date('Y-m-d g:i a', strtotime($user->created)),
				);

			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}
}
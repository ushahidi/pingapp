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

	public function action_email()
	{
		$this->template = '';
		$this->auto_render = FALSE;
	 
		// Use App's Email Setup to Send Mail
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
		
		$body = View::factory('email/layout');
	 
		$email = Email::factory('Test Email', $body->render(), 'text/html')
			->to('seth@ushahidi.com')
			->from('seth@ushahidi.com', 'Seth')
			->send();
	 
		echo $body;
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

		// Pings
		$pings = DB::select('cp.person_id', 'p.name', 'p.status', 'pings.type', 'c.contact', array('pings.created', 'created_on'), array(DB::expr('"ping"'), 'action'), array(DB::expr('pings.message_id'), 'message_id'))
			->from('pings')
			->join(array('contacts', 'c'), 'INNER')
				->on('pings.contact_id', '=', 'c.id')
			->join(array('contacts_people', 'cp'), 'INNER')
				->on('c.id', '=', 'cp.contact_id')
			->join(array('people', 'p'), 'INNER')
				->on('cp.person_id', '=', 'p.id')
			->join(array('messages', 'm'), 'INNER')
				->on('pings.message_id', '=', 'm.id')
			->where('p.user_id', '=', $this->user->id)
			->where('m.user_id', '=', $this->user->id); // Ensure one can only view pings they sent

		// Pongs
		$pongs = DB::select('cp.person_id', 'p.name', 'p.status', 'pongs.type', 'c.contact', array('pongs.created', 'created_on'), array(DB::expr('"pong"'), 'action'), array(DB::expr('0'), 'message_id'))
			->from('pongs')
			->join(array('contacts', 'c'), 'INNER')
				->on('pongs.contact_id', '=', 'c.id')
			->join(array('contacts_people', 'cp'), 'INNER')
				->on('c.id', '=', 'cp.contact_id')
			->join(array('people', 'p'), 'INNER')
				->on('cp.person_id', '=', 'p.id')
			->join(array('pings', 'pi'), 'INNER')
				->on('pongs.ping_id', '=', 'pi.id')
			->join(array('messages', 'm'), 'INNER')
				->on('pi.message_id', '=', 'm.id')
			->where('p.user_id', '=', $this->user->id)
			->where('m.user_id', '=', $this->user->id); // Ensure one can only view pongs received by their contacts

		// Paging
		$offset = $limit = '';
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$limit = ' LIMIT '.(int) $_GET['iDisplayLength'];
			$offset = ' OFFSET '.(int) $_GET['iDisplayStart'];
		}

		$order = ' ORDER BY 6 DESC';

		// Message ID?
		if ( isset( $_GET['message_id'] ) )
		{
			$pings->having('message_id', '=', (int) $_GET['message_id']);
			$pongs->having('message_id', '=', (int) $_GET['message_id']);
		}

		$items = DB::query(Database::SELECT, '('.$pings.') UNION ALL ('.$pongs.') '.$order.$limit.$offset)->execute();
		$total = DB::query(Database::SELECT, '('.$pings.') UNION ALL ('.$pongs.') ')->execute();

		//Output
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => count($items),
			"iTotalDisplayRecords" => count($total),
			"aaData" => array()
		);

		foreach ($items as $item)
		{
			$pong_label = ($item['action'] == 'pong') ? '' : 'secondary';
			$row = array(
				0 => '<a href="/people/view/'.$item['person_id'].'"><strong>'.strtoupper($item['name']).'</strong></a>',
				1 => Text::limit_chars(strtoupper($item['contact']), '8', '...'),
				2 => '<span class="radius label secondary">'.strtoupper($item['type']).'</status>',
				3 => '<span class="radius label '.$pong_label.'">'.strtoupper($item['action']).'</status>',
				4 => date('Y-m-d g:i a', strtotime($item['created_on'])),
				);

			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}
}

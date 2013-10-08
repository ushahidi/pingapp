<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Ping Tasks
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tasks
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Task_Ping extends Minion_Task
{
	protected $_options = array(
		'subtask' => FALSE,
	);

	/**
	 * Execute Sub Task
	 *
	 * @return null
	 */
	protected function _execute(array $params)
	{
		switch ($params['subtask']) {
			case 'queue':
				$this->_queue();
				break;

			case 'requeue':
				$this->_requeue();
				break;

			case 'process':
				$this->_process();
				break;
			
			default:
				
				break;
		}
	}

	/**
	 * Queue Up Pings To Be Sent
	 *
	 * @return null
	 */
	protected function _queue()
	{
		// Initialize Redis
		$redis = PingApp_Redis::factory();

		// Get All Unsent Pings
		$pings = ORM::factory('Ping')
			->where('status', '=', 'pending')
			->where('sent', '!=', 1)
			->find_all();

		foreach ($pings as $ping)
		{
			$redis->rpush('pingapp_pings', $ping->id);
		}
	}

	/**
	 * Re-Queue Up Pings To Be Sent
	 *
	 * Look for all pings without a pong within a 10 minute timeframe
	 * if exists, create a new ping for the queue
	 *
	 * @return null
	 */
	protected function _requeue()
	{
		// Get Pongs
		$pongs = DB::select('cp.contact_id', array(DB::expr('COUNT(pongs.id)'), 'pongs'))
		    ->from('pongs')
		    ->join(array('contacts', 'c'), 'INNER')
		    	->on('pongs.contact_id', '=', 'c.id')
		    ->join(array('contacts_people', 'cp'), 'INNER')
		    	->on('c.id', '=', 'cp.contact_id')
		    ->group_by('cp.contact_id');

		// Get Pings
		$pings = ORM::factory('Ping')
			->select('pongs.pongs')
			->where('sent', '=', 1)
			->where('status', '=', 'pending')
			->where('parent_id', '=', 0)
			->join(array($pongs, 'pongs'), 'LEFT')
		    	->on('ping.contact_id', '=', 'pongs.contact_id')
		    ->where('ping.updated', '<=', DB::expr('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'))
		    ->where('ping.updated', '>=', DB::expr('DATE_SUB(NOW(), INTERVAL 1 DAY)'))
			->find_all();

		foreach ($pings as $ping)
		{
			// Requeue only if 10 mins have passed
			// Requeue only if the original is less than 24 hours old
			if ( (int) $ping->pongs == 0 )
			{
				$new_ping = ORM::factory('Ping')
					->where('parent_id', '=', $ping->id)
					->where('status', '=', 'pending')
					->where('sent', '!=', 1)
					->find();

				if ( ! $new_ping->loaded() )
				{
					$new_ping->values(array(
							'parent_id' => $ping->id,
							'message_id' => $ping->message_id,
							'tracking_id' => '0',
							'type' => $ping->type,
							'contact_id' => $ping->contact_id,
							'provider' => $ping->provider,
							'status' => 'pending',
							'sent' => 0
						));
					$new_ping->save();
				}
			}
		}
	}

	/**
	 * Process all items in the ping queue
	 *
	 * @return null
	 */
	protected function _process()
	{
		// Initialize Redis
		$redis = PingApp_Redis::factory();

		$max_items = 20; // Max items to process per run

		for ($i=0; $i < $max_items; $i++)
		{
			try
			{
				// Pop An Element from the Queue
				$item = $redis->lPop('pingapp_pings');

				if ( (int) $item )
				{
					PingApp_Ping::process((int) $item);
				}
				else
				{
					// Nothing to process
					break;
				}

				$i++;
			}
			catch (Exception $e)
			{
				break;
			}
		}
	}
}
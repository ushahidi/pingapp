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
				// Pop An Element from the Queue (Block for 3 seconds)
				$item = $redis->blPop('pingapp_pings', 3);

				if ( isset($item[1]) )
				{
					PingApp_Ping::process((int) $item[1]);
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
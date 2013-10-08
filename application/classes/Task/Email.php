<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Email Retrieval Tasks
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tasks
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Task_Email extends Minion_Task
{
	protected $_options = array(
		'subtask' => FALSE,
	);

	/**
	 * Check Email Account For Replies
	 *
	 * @return null
	 */
	protected function _execute(array $params)
	{
		
	}
}
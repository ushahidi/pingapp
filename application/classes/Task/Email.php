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
		$type = PingApp_Settings::get('email_incoming_type');
		$server = PingApp_Settings::get('email_incoming_server');
		$port = PingApp_Settings::get('email_incoming_port');
		$encryption = PingApp_Settings::get('email_incoming_security');
		$username = PingApp_Settings::get('email_incoming_username');
		$password = PingApp_Settings::get('email_incoming_password');

		// Encryption type
		$encryption = ($encryption != 'none') ? '/'.$encryption : '';

		try
		{
			// Try to connect
			$connection = imap_open('{'.$server.':'.$port.'/'.$type.$encryption.'}INBOX', $username, $password);

			$emails = imap_search($connection,'ALL');
			if ($emails)
			{
				// reverse sort emails?
				//rsort($emails);

				foreach($emails as $email_number)
				{
					$overview = imap_fetch_overview($connection, $email_number, 0);
					$message = imap_fetchbody($connection, $email_number, 2);

					// Process the email
					PingApp_Email::process($overview, $message);

					// After processing, delete!
					imap_delete($connection, $email_number);
				}
			}
		}
		catch (Exception $e)
		{
			Kohana::$log->add(Log::ERROR, $e->getMessage());
		}
	}
}
<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Parse Helper
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Classes
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class PingApp_Parse {

	/**
	 * @todo ** MOVE THESE TO SETTINGS TABLE **
	 * 
	 * @var array okay keywords
	 */
	static protected $oks = array('ok', 'okay', 'fine');

	/**
	 * @var array not okay keywords
	 */
	static protected $notoks = array('not ok', 'not okay', 'notokay');

	/**
	 * Parse For Status
	 *
	 * @param object $contact
	 * @param object $pong
	 * @param string $message
	 * @return void
	 */
	public static function status($contact = NULL, $pong = NULL, $message = NULL)
	{
		$status = 'unknown';
		if ( $contact->loaded() AND $pong->loaded() AND $message )
		{
			foreach (self::$oks as $ok)
			{
				if ( preg_match("/\b".$ok."\b/i", $message) )
				{
					$status = 'ok';
				}
			}

			foreach (self::$notoks as $notok)
			{
				if ( preg_match("/\b".$notok."\b/i", $message) )
				{
					$status = 'notok';
				}
			}

			if ($status !== 'uknown')
			{
				// Find All People Associated With This Contact
				$people = $contact->people->find_all();
				foreach ($people as $person)
				{
					$person->status = $status;
					$person->save();

					// Log this in person_statuses as well
					$person_status = ORM::factory('Person_Status');
					$person_status->person_id = $person->id;
					$person_status->pong_id = $pong->id;
					$person_status->status = $status;
					$person_status->save();
				}
			}
		}
	}
}
<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Person Status Helper
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Classes
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class PingApp_Status {

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
	public static function parse($contact = NULL, $pong = NULL, $message = NULL)
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

			if ($status !== 'unknown')
			{
				// Find All Primary People Associated With This Contact
				$people = $contact->people
					->where('parent_id', '=', 0)
					->find_all();
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

	/**
	 * Update status manually
	 *
	 * @param object $user - user making the update
	 * @param object $person - person user is making the update on
	 * @param string $status - the new status
	 * @param string $note - attach a note to update
	 * @return void
	 */
	public static function update($user = NULL, $person = NULL, $status = NULL, $note = NULL)
	{
		if ( $user->loaded() AND $person->loaded() AND $status)
		{
			// Get All Contacts
			$contacts = $person->contacts->find_all();
			foreach ($contacts as $contact)
			{
				if ( $contact->loaded() )
				{
					// Find All Primary People Associated With This Contact
					$people = $contact->people
						->where('parent_id', '=', 0)
						->find_all();
					foreach ($people as $person)
					{
						$person->status = $status;
						$person->save();

						// Log this in person_statuses as well
						$person_status = ORM::factory('Person_Status');
						$person_status->person_id = $person->id;
						$person_status->user_id = $user->id;
						$person_status->status = $status;
						$person_status->note = $note;
						$person_status->save();
					}
				}
			}
		}
	}
}
<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Incoming Email Helper
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Classes
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class PingApp_Email {

	/**
	 * Process incoming emails
	 *
	 * @param object $overview
	 * @param string message - the email message
	 */
	public static function process($overview, $message)
	{
		$from = self::get_email($overview[0]->from);
		$tracking_id = self::tracking_id($message);

		$contact = Model_Contact::get_contact($from, 'email');
		if ( ! $contact)
		{
			// HALT
			Kohana::$log->add(Log::ERROR, __("':from' is not registered as a contact", array(":from" => $from)));
			return;
		}

		if ( ! trim($message))
		{
			// HALT
			Kohana::$log->add(Log::ERROR, __("blank message received"));
			return;
		}

		// Get Original Ping
		$ping = self::get_ping($tracking_id, $contact);
		
		// Record the pong
		if ( $ping->loaded() )
		{
			// Mark the ping as replied
			$ping->set('status', 'replied')->save();

			// strip all html
			$content = trim(strip_tags($message, ""));

			// convert all HTML entities to their applicable characters
			$content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
			
			$pong = ORM::factory('Pong')
				->values(array(
					'content' => $content,
					'contact_id' => $contact->id,
					'type' => 'email',
					'ping_id' => $ping->id
				))
				->save();
			
			// Lets parse the message for OK/NOT OKAY indicators
			PingApp_Status::parse($contact, $pong, $message);

			return TRUE;
		}
		else
		{
			Kohana::$log->add(Log::ERROR, __("There is no record of ':from' having been pinged",
				array(":from" => $from)));
		}
	}

	/**
	 * Extract the tracking id from an incoming email
	 *
	 * @param string $body body of the email
	 * @return string tracking_id or null
	 */
	public static function tracking_id($body)
	{
		$pattern = '(\\[)(\\{)((?:[a-z][a-z]*[0-9]+[a-z0-9]*))(\\})(\\])';
		if ( preg_match_all ("/".$pattern."/is", $body, $matches) )
		{
			$tracking_id = $matches[3][0];

			return $tracking_id;
		}
		
		return NULL;
	}

	/**
	 * Extract the FROM email address string
	 *
	 * @param string $from - from address string from email
	 * @return string email address or NULL
	 */
	public static function get_email($from)
	{
		$pattern = '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i';
		
		if ( preg_match_all($pattern, $from, $emails) )
		{
			foreach ($emails as $key => $value)
			{
				if (isset($value[0]))
				{
					return $value[0];
				}
			}
		}

		return NULL;
	}

	/**
	 * Get the original ping
	 *
	 * @param string $tracking_id
	 * @param object $contact
	 * 
	 * @return object $ping
	 */
	public static function get_ping($tracking_id = NULL, $contact = NULL)
	{
		if ($tracking_id)
		{
			return ORM::factory('Ping')
				->where('type', '=', 'email')
				->where('sent', '=', 1)
				->where('tracking_id', '=', $tracking_id)
				->find();
		}

		return ORM::factory('Ping')
			->where('contact_id', '=', $contact->id)
			->where('type', '=', 'email')
			->where('sent', '=', 1)
			->where('parent_id', '=', 0)
			->order_by('created', 'DESC')
			->find();
	}
}
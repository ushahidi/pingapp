<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Ping Helper
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Classes
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class PingApp_Ping {
	
	public static function process($ping_id = 0)
	{
		if ($ping_id)
		{
			// 1. Ping Exists, Is Pending and not Sent?
			$ping = ORM::factory('Ping')
				->where('id', '=', $ping_id)
				->where('status', '=', 'pending')
				->where('sent', '!=', 1)
				->find();

			if ( $ping->loaded() )
			{
				// Is this an old Ping? (> 24 hours old) Expire It.
				if ( ( time() - strtotime($ping->created) ) > 86400)
				{
					$ping->status = 'expired';
					$ping->save();

					return;
				}

				// 2. Contacts Exists?
				$contact = $ping->contact;
				if ( $contact->loaded() )
				{
					// How many times has this contact been pinged
					// in the last 24 hours
					$pings = $contact->pings
						->where('sent', '=', 1)
						->where('created', '>=', DB::expr('DATE_SUB(NOW(), INTERVAL 1 DAY)'))
						->order_by('created', 'DESC')
						->find_all();

					// Get the pings per 24 hours setting
					$pings_per_24 = (int) PingApp_Settings::get('pings_per_24');
					// Prevent Abuse - Use 10 Max
					if ( ! $pings_per_24 OR $pings_per_24 > 10)
					{
						$pings_per_24 = 3;
					}
					if ($pings->count() < $pings_per_24)
					{
						// But we need to space the pings out
						if ($pings->count() > 0)
						{
							// Get the last ping to this contact
							foreach ($pings as $_ping)
							{
								// Get the Reping Delay
								$pings_repings_delay = (int) PingApp_Settings::get('pings_repings_delay');
								if ( ! $pings_repings_delay OR $pings_repings_delay < 5)
								{
									$pings_repings_delay = 5;
								}
								
								if ( ( time() - strtotime($_ping->updated) ) < ($pings_repings_delay * 60) )
								{
									return;
								}

								break;
							}
						}

						// Phew - Okay we can now send this ping
						if ($ping->type == 'sms')
						{
							self::_sms($ping, $contact);
						}
						elseif ($ping->type == 'email')
						{
							self::_email($ping, $contact);
						}
					}
					// Else its time to contact secondary folks
					// or just STOP!!! No More Messages!
					else
					{
						// Cancel All Pending Pings to this Contact
						$pending_pings = $contact->pings
							->where('status', '=', 'pending')
							->where('sent', '!=', 1)
							->find_all();

						foreach ($pending_pings as $_ping)
						{
							$_ping->status = 'cancelled';
							$_ping->save();

							// Cancel the Parent Too
							$parent = $_ping->parent;
							if ( $parent->loaded() )
							{
								$parent->status = 'cancelled';
								$parent->save();

								// If there are any other children out there
								foreach ($parent->children->find_all() as $child)
								{
									$child->status = 'cancelled';
									$child->save();
								}
							}
						}
						
						// Ping the Secondaries
						foreach ($contact->children as $secondary)
						{
							$secondary_ping = ORM::factory('Ping')
								->where('parent_id', '=', $ping->id)
								->where('contact_id', '=',$secondary->contact_id)
								->where('status', '=', 'pending')
								->where('sent', '!=', 1)
								->find();

							if ( ! $secondary_ping->loaded() )
							{
								foreach ($secondary->contacts->find_all() as $_contact)
								{
									$secondary_ping->values(array(
											'parent_id' => $ping->id,
											'message_id' => $ping->message_id,
											'tracking_id' => '0',
											'type' => $_contact->type,
											'contact_id' => $_contact->id,
											'provider' => 0, // Will be updated after a successful send
											'status' => 'pending',
											'sent' => 0
										));
									$secondary_ping->save();
								}
							}
						}
					}
				}
			}
		}
	}

	private static function _sms($ping, $contact)
	{
		// Get the SMS provider to use
		try
		{
			$provider = PingApp_SMS_Provider::instance();
		}
		catch (PingApp_Exception $e)
		{
			// Failed
			$ping->status = 'failed';
			$ping->save();

			Kohana::$log->add(Log::ERROR, $e->getMessage());
		}

		$tagline = PingApp_Settings::get('message_sms');

		if (($tracking_id = $provider->send($contact->contact, $ping->message->message.' '.$tagline)) !== FALSE)
		{
			$ping->tracking_id = $tracking_id;
			$ping->provider = $provider::$sms_provider; // Update the provider in case its changed
			$ping->sent = 1;
			$ping->save();
		}
		else
		{
			// Failed
			$ping->status = 'failed';
			$ping->save();
		}
	}

	private static function _email($ping, $contact)
	{
		$driver = PingApp_Settings::get('email_outgoing_type');
		$options = array(
			'hostname' => PingApp_Settings::get('email_outgoing_host'),
			'port' => PingApp_Settings::get('email_outgoing_port'),
			'encryption' => (PingApp_Settings::get('email_outgoing_security') != 'none') 
				? PingApp_Settings::get('email_outgoing_security') : '',
			'username' => PingApp_Settings::get('email_outgoing_username'),
			'password' => PingApp_Settings::get('email_outgoing_password')
			);

		$tracking_id = self::tracking_id('email');

		$config = Kohana::$config->load('email');
		$config->set('driver', $driver);
		$config->set('options', $options);

		$title = $ping->message->title;

		$person = $contact->people->order_by('created', 'ASC')->find();

		$sender_name = $ping->message->user->first_name.' '.$ping->message->user->last_name;
		$sender_email = $ping->message->user->email;
		$sender = ($sender_name != ' ') ? $sender_name : $sender_email;


		$body = View::factory('email/layout');
		$body->name = $person->name;
		$body->message = $ping->message->message;
		$body->sender = $ping->message->user;
		$body->tracking_id = $tracking_id;
		$body->site_url = PingApp_Settings::get('site_url');


		$from = PingApp_Settings::get('email_from');
		$from_name = PingApp_Settings::get('email_from_name');

		try
		{
			$result = Email::factory($title, $body->render(), 'text/html')
				->to($contact->contact)
				->from($from, $sender)
				->send();

			$ping->provider = (PingApp_Settings::get('email_outgoing_host')) ? PingApp_Settings::get('email_outgoing_host') : 'email';
			$ping->tracking_id = $tracking_id;
			$ping->sent = 1;
			$ping->save();
		}
		catch (Exception $e)
		{
			// Failed
			$ping->status = 'failed';
			$ping->save();

			Kohana::$log->add(Log::ERROR, $e->getMessage());
		}
	}

	/**
	 * Generate A Tracking ID for email, smssync, etc
	 *
	 * @param string $type - type of tracking_id
	 * @return string tracking id
	 */
	public static function tracking_id($type = 'email')
	{
		$unique = FALSE;
		$code = NULL;
		while ( ! $unique)
		{
			$code = Text::random('alnum', 16);
			$ping = ORM::factory('Ping')
				->where('type', '=', $type)
				->where('tracking_id', '=', $code)
				->find();

			if ( ! $ping->loaded() )
			{
				$unique = TRUE;
			}
		}

		return $code;
	}
}
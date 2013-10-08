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

					// If less than 3, we can safely proceed
					if ($pings->count() < 3)
					{
						// But we need to space the pings out
						if ($pings->count() > 0)
						{
							// Get the last ping to this contact
							foreach ($pings as $_ping)
							{
								// 10 minute spacer
								if ( ( time() - strtotime($_ping->updated) ) < 600)
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
						// Cancel All Pings to this Contact
						foreach ($pings as $_ping)
						{
							$_ping->status = 'cancelled';
							$_ping->save();
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

		if (($tracking_id = $provider->send($contact->contact, $ping->message->message)) !== FALSE)
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
		
	}
}
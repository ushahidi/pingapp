<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_1_20131009141005 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Add default email message template
		PingApp_Settings::set('message_email', "Dear {{name}},\n\n{{message}}\n\n~~~~~~~\nsent from pingapp.io");

		// Add default sms tagline
		PingApp_Settings::set('message_sms', "- sent from pingapp.io");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		// $db->query(NULL, 'DROP TABLE ... ');
	}

}

<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_1_20131010174406 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Max Pings Per 24
		PingApp_Settings::set('pings_per_24', "5");

		// Re-Ping Delay
		PingApp_Settings::set('pings_repings_delay', "10");
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
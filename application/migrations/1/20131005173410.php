<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_1_20131005173410 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `pings` CHANGE `status` `status` VARCHAR(20)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT 'pending'  COMMENT 'pending, sent, received, replied, expired';");

		$db->query(NULL, "ALTER TABLE `pings` ADD `updated` DATETIME  NOT NULL  DEFAULT '1001-01-01 00:00:00'  AFTER `created`;");

	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `pings` CHANGE `status` `status` VARCHAR(20)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT 'pending'  COMMENT 'pending, received';");

		$db->query(NULL, "ALTER TABLE `pings` DROP `updated`;");
	}

}

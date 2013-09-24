<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_1_20130924131843 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Add status column to people table
		$db->query(NULL, "ALTER TABLE `people` ADD `status` VARCHAR(30)  NOT NULL  DEFAULT 'unknown'  COMMENT 'ok, notok'  AFTER `last_name`;");

		// Add index to status column
		$db->query(NULL, "ALTER TABLE `people` ADD INDEX `idx_status` (`status`);");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		// Drop status column
		$db->query(NULL, "ALTER TABLE `people` DROP `status`;");
	}
}

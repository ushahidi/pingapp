<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_1_20131008180256 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `messages` ADD `title` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `user_id`;");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `messages` DROP `title`;");
	}

}

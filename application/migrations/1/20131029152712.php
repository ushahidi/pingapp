<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_1_20131029152712 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `person_statuses` ADD `user_id` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0'  AFTER `pong_id`;");
		$db->query(NULL, "ALTER TABLE `person_statuses` ADD INDEX `idx_user_id` (`user_id`);");
		$db->query(NULL, "ALTER TABLE `person_statuses` ADD `note` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `status`;");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `person_statuses` DROP `user_id`;");
		$db->query(NULL, "ALTER TABLE `person_statuses` DROP `note`;");
	}
}

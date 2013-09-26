<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_1_20130926181551 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// We don't need to associate pongs back to any particular person_id
		// Multiple users might have the same contact under different names
		$db->query(NULL, "ALTER TABLE `pongs` DROP `person_id`;");

		// We don't need a user_id in person_statuses any more
		$db->query(NULL, "ALTER TABLE `person_statuses` DROP `user_id`;");


		// We can however associate pongs it with a particular contact
		$db->query(NULL, "ALTER TABLE `pongs` ADD `contact_id` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0'  AFTER `ping_id`;");
		$db->query(NULL, "ALTER TABLE `pongs` ADD INDEX `idx_contact_id` (`contact_id`);");

		// Settings Table
		$db->query(NULL, "DROP TABLE IF EXISTS `settings`;");
		$db->query(NULL, "CREATE TABLE `settings` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `key` varchar(100) NOT NULL DEFAULT '',
		  `value` varchar(255) DEFAULT NULL,
		  `created` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `pongs` ADD `person_id` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0'  AFTER `ping_id`;");
		$db->query(NULL, "ALTER TABLE `pongs` ADD INDEX `idx_person_id` (`person_id`);");
		$db->query(NULL, "ALTER TABLE `pongs` DROP `contact_id`;");

		$db->query(NULL, "ALTER TABLE `person_statuses` ADD `user_id` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0'  AFTER `person_id`;");
		$db->query(NULL, "ALTER TABLE `person_statuses` ADD INDEX `idx_user_id` (`user_id`);");

		$db->query(NULL, "DROP TABLE IF EXISTS `settings`;");
	}

}

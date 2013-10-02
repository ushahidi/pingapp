<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_1_20131001183143 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		/**
		 * Groups_People Table
		 */
		$db->query(NULL, "DROP TABLE IF EXISTS `groups_people`;");
		$db->query(NULL, "CREATE TABLE `groups_people` (
		  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `person_id` int(11) unsigned NOT NULL,
		  PRIMARY KEY (`group_id`,`person_id`),
		  CONSTRAINT `fk_groups_people_group_id` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
		  CONSTRAINT `fk_groups_people_person_id` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		// Drop group_id column from people
		$db->query(NULL, "ALTER TABLE `people` DROP `group_id`;");

	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `people` ADD `group_id` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0'  AFTER `user_id`;");
		$db->query(NULL, "ALTER TABLE `people` ADD INDEX `idx_group_id` (`group_id`);");

		$db->query(NULL, "DROP TABLE IF EXISTS `groups_people`;");
	}

}

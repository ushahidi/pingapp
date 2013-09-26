<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_1_20130926120600 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		/**
		 * Contacts Table
		 */
		$db->query(NULL, "DROP TABLE IF EXISTS `contacts`;");
		$db->query(NULL, "CREATE TABLE `contacts` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `contact` varchar(200) DEFAULT NULL,
		  `type` varchar(20) DEFAULT NULL COMMENT 'email, phone, twitter',
		  `created` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  PRIMARY KEY (`id`),
		  KEY `unq_contact_type` (`contact`,`type`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		/**
		 * Contacts_People Table
		 */
		$db->query(NULL, "DROP TABLE IF EXISTS `contacts_people`;");
		$db->query(NULL, "CREATE TABLE `contacts_people` (
		  `contact_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `person_id` int(11) unsigned NOT NULL DEFAULT '0',
		  PRIMARY KEY (`contact_id`,`person_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		// Alter Pings Table
		$db->query(NULL, "ALTER TABLE `pings` ADD `contact_id` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0'  AFTER `tracking_id`;");
		$db->query(NULL, "ALTER TABLE `pings` ADD INDEX `idx_contact_id` (`contact_id`);");

		// Ping Statuses are now 'unsent, sent, received'
		$db->query(NULL, "ALTER TABLE `pings` CHANGE `status` `status` VARCHAR(20)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT 'sent'  COMMENT 'unsent, sent, received';");

		// Drop unncessary column
		$db->query(NULL, "ALTER TABLE `pings` DROP `person_contact_id`;");

		// Drop unncessary table
		$db->query(NULL, "DROP TABLE IF EXISTS `person_contacts`;");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "DROP TABLE IF EXISTS `contacts_people`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `contacts`;");
	}

}

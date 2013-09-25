<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_1_20130925044750 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Providers
		$db->query(NULL, 'DROP TABLE IF EXISTS `providers`;');
		
		// Pings
		$db->query(NULL, 'ALTER TABLE `pings` DROP INDEX `idx_provider_id`;');
		$db->query(NULL, 'ALTER TABLE `pings` CHANGE `provider_id` `provider` VARCHAR(20)  NOT NULL  DEFAULT '0';');
		$db->query(NULL, 'ALTER TABLE `pings` ADD COLUMN `tracking_id` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `message_id`;');
		$db->query(NULL, 'ALTER TABLE `pings` ADD UNIQUE INDEX `idx_provider_tracking_id` (`tracking_id`, `provider`);');
		$db->query(NULL, 'ALTER TABLE `pings` DROP INDEX `idx_person_id`;');
		$db->query(NULL, 'ALTER TABLE `pings` DROP `person_id`;');
		
		// Pongs
		$db->query(NULL, 'ALTER TABLE `pongs` ADD `ping_id` INT(11) UNSIGNED  NOT NULL AFTER `id`;');
		$db->query(NULL, 'ALTER TABLE `pongs` ADD INDEX `idx_ping_id` (`ping_id`);');
		
		// Person contacts
		$db->query(NULL, 'ALTER TABLE `person_contacts` ADD UNIQUE INDEX `idx_contact_type` (`contact`, `type`);');
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "CREATE TABLE IF NOT EXISTS `providers` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(200) DEFAULT NULL,
		  `username` varchar(100) DEFAULT NULL,
		  `api_key` varchar(100) DEFAULT NULL,
		  `created` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  `updated` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		// Pings
		$db->query(NULL, 'ALTER TABLE `pings` CHANGE `provider` `provider_id` INT(11)  UNSIGNED NOT NULL  DEFAULT '0';');
		$db->query(NULL, 'ALTER TABLE `pings` ADD INDEX `idx_provider_id` (`provider_id`);');
		$db->query(NULL, 'ALTER TABLE `pings` DROP `tracking_id`;');
		$db->query(NULL, 'ALTER TABLE `pings` DROP INDEX `idx_provider_tracking_id`;');
		$db->query(NULL, 'ALTER TABLE `pings` ADD `person_id` INT(11) UNSIGNED NOT NULL;');
		$db->query(NULL, 'ALTER TABLE `pings` ADD INDEX `idx_person_id` (`person_id`);');
		
		// Pongs
		$db->query(NULL, 'ALTER TABLE `pongs` DROP INDEX `idx_ping_id`;');
		$db->query(NULL, 'ALTER TABLE `pongs` DROP `ping_id`;');
		
		// Person contact
		$db->query(NULL, 'ALTER TABLE `person_contacts` DROP INDEX `idx_contact_type`;');
	}

}

<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_1_20131005173410 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `pings` CHANGE `status` `status` VARCHAR(20)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT 'pending'  COMMENT 'pending, received, expired, cancelled, failed';");
		$db->query(NULL, "ALTER TABLE `pings` ADD `sent` TINYINT(1)  NOT NULL  DEFAULT '0'  AFTER `status`;");
		$db->query(NULL, "ALTER TABLE `pings` ADD `updated` DATETIME  NOT NULL  DEFAULT '1001-01-01 00:00:00'  AFTER `created`;");
		$db->query(NULL, "ALTER TABLE `pings` ADD `parent_id` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0'  AFTER `id`;");
		$db->query(NULL, "ALTER TABLE `pings` ADD INDEX `idx_parent_id` (`parent_id`);");
		$db->query(NULL, "ALTER TABLE `pings` CHANGE `type` `type` VARCHAR(20)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT 'phone'  COMMENT 'email, sms, twitter';");
		$db->query(NULL, "ALTER TABLE `pongs` CHANGE `type` `type` VARCHAR(20)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT 'phone'  COMMENT 'email, sms, voice, twitter';");
		$db->query(NULL, "ALTER TABLE `messages` CHANGE `type` `type` VARCHAR(20)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT 'phone'  COMMENT 'email, sms, twitter';");
		$db->query(NULL, "ALTER TABLE `pings` DROP INDEX `idx_provider_tracking_id`;");
		$db->query(NULL, "ALTER TABLE `pings` ADD INDEX `idx_tracking_id` (`tracking_id`);");
		$db->query(NULL, "ALTER TABLE `pings` ADD INDEX `idx_provider` (`provider`);");


		// Migrate types
		$this->_migrate_old();
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `pings` CHANGE `status` `status` VARCHAR(20)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT 'pending'  COMMENT 'pending, received';");
		$db->query(NULL, "ALTER TABLE `pings` CHANGE `type` `type` VARCHAR(20)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT 'phone'  COMMENT 'email, phone, twitter';");
		$db->query(NULL, "ALTER TABLE `pings` DROP `sent`;");
		$db->query(NULL, "ALTER TABLE `pings` DROP `updated`;");
		$db->query(NULL, "ALTER TABLE `pings` DROP `parent_id`;");
		$db->query(NULL, "ALTER TABLE `pongs` CHANGE `type` `type` VARCHAR(20)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT 'phone'  COMMENT 'email, phone, twitter';");
		$db->query(NULL, "ALTER TABLE `messages` CHANGE `type` `type` VARCHAR(20)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT 'phone'  COMMENT 'email, phone, twitter';");

		// Migrate types
		$this->_un_migrate_old();
	}

	/**
	 * Migrate Old Types
	 * @return void
	 */
	private function _migrate_old()
	{
		$pings = ORM::factory('Ping')
			->where('type', '=', 'phone')
			->find_all();

		foreach ($pings as $ping)
		{
			$ping->type = 'sms';
			$ping->save();
		}

		$pongs = ORM::factory('Pong')
			->where('type', '=', 'phone')
			->find_all();

		foreach ($pongs as $pong)
		{
			$pong->type = 'sms';
			$pong->save();
		}

		$messages = ORM::factory('Message')
			->where('type', '=', 'phone')
			->find_all();

		foreach ($messages as $message)
		{
			$message->type = 'sms';
			$message->save();
		}
	}

	/**
	 * UnMigrate Old Type
	 * @return void
	 */
	private function _un_migrate_old()
	{
		$pings = ORM::factory('Ping')
			->where('type', '=', 'sms')
			->find_all();

		foreach ($pings as $ping)
		{
			$ping->type = 'phone';
			$ping->save();
		}

		$pongs = ORM::factory('Pong')
			->where('type', '=', 'sms')
			->find_all();

		foreach ($pongs as $pong)
		{
			$pong->type = 'phone';
			$pong->save();
		}

		$messages = ORM::factory('Message')
			->where('type', '=', 'sms')
			->find_all();

		foreach ($messages as $message)
		{
			$message->type = 'phone';
			$message->save();
		}
	}

}

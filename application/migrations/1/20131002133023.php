<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_1_20131002133023 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		/**
		 * Settings Table
		 */
		$db->query(NULL, "DROP TABLE IF EXISTS `settings`;");
		$db->query(NULL, "CREATE TABLE `settings` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '0 = Global',
		  `key` varchar(100) NOT NULL DEFAULT '',
		  `value` text,
		  `created` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  `updated` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `unq_user_id_key` (`user_id`,`key`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "DROP TABLE IF EXISTS `settings`;");
	}

}

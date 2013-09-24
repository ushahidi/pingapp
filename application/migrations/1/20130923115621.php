<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_1_20130923115621 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Drop all Tables First
		$db->query(NULL, "DROP TABLE IF EXISTS `groups`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `messages`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `person_contacts`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `person_statuses`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `people`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `pings`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `pongs`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `providers`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `roles_users`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `roles`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `users`;");

		/**
		 * Groups table - group people
		 */
		$db->query(NULL, "CREATE TABLE `groups` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT, 
		  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `name` varchar(255) DEFAULT NULL,
		  `created` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  `updated` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  PRIMARY KEY (`id`),
		  KEY `idx_user_id` (`user_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


		/**
		 * Messages - created to send to people
		 */
		$db->query(NULL, "CREATE TABLE `messages` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `message` text,
		  `type` varchar(20) NOT NULL DEFAULT 'phone' COMMENT 'email, phone, twitter',
		  `created` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  `updated` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  PRIMARY KEY (`id`),
		  KEY `idx_user_id` (`user_id`),
		  KEY `idx_type` (`type`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		/**
		 * People
		 */
		$db->query(NULL, "CREATE TABLE `people` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `first_name` varchar(100) DEFAULT NULL,
		  `last_name` varchar(100) DEFAULT NULL,
		  `created` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  `updated` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  PRIMARY KEY (`id`),
		  KEY `idx_group_id` (`group_id`),
		  KEY `idx_user_id` (`user_id`),
		  KEY `idx_parent_id` (`parent_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		/**
		 * Person_Contacts -- Add multiple contact details to people (above)
		 */
		$db->query(NULL, "CREATE TABLE `person_contacts` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `person_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `contact` varchar(200) DEFAULT NULL,
		  `type` varchar(20) DEFAULT NULL COMMENT 'email, phone, twitter',
		  `created` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  `updated` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  PRIMARY KEY (`id`),
		  KEY `idx_type` (`type`),
		  KEY `fk_person_contacts_person_id` (`person_id`),
		  CONSTRAINT `fk_person_contacts_person_id` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		/**
		 * Person_Statuses -- Add multiple status updates to people based on response back
		 * OK, NOT OKAY, UNKNOWN
		 */
		$db->query(NULL, "CREATE TABLE `person_statuses` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `person_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `pong_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `status` varchar(30) DEFAULT 'unknown' COMMENT 'ok, notok',
		  `created` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  PRIMARY KEY (`id`),
		  KEY `fk_person_statuses_person_id` (`person_id`),
		  KEY `idx_pong_id` (`pong_id`),
		  KEY `idx_user_id` (`user_id`),
		  KEY `idx_status` (`status`),
		  CONSTRAINT `fk_person_statuses_person_id` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		/**
		 * Pings - Message to a specific person
		 */
		$db->query(NULL, "CREATE TABLE `pings` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `message_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `person_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `person_contact_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `provider_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `type` varchar(20) NOT NULL DEFAULT 'phone' COMMENT 'email, phone, twitter',
		  `status` varchar(20) NOT NULL DEFAULT 'pending' COMMENT 'pending, received',
		  `created` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  PRIMARY KEY (`id`),
		  KEY `idx_message_id` (`message_id`),
		  KEY `idx_person_id` (`person_id`),
		  KEY `idx_person_contact_id` (`person_contact_id`),
		  KEY `idx_provider_id` (`provider_id`),
		  KEY `idx_type` (`type`),
		  KEY `idx_status` (`status`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		/**
		 * Pongs - Message back from a specific person
		 */
		$db->query(NULL, "CREATE TABLE `pongs` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `person_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `content` text,
		  `type` varchar(20) NOT NULL DEFAULT 'phone' COMMENT 'email, phone, twitter',
		  `created` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  PRIMARY KEY (`id`),
		  KEY `idx_person_id` (`person_id`),
		  KEY `idx_type` (`type`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		/**
		 * Providers - SMS, WhatsApp, etc
		 */
		$db->query(NULL, "CREATE TABLE `providers` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(200) DEFAULT NULL,
		  `username` varchar(100) DEFAULT NULL,
		  `api_key` varchar(100) DEFAULT NULL,
		  `created` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  `updated` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		/**
		 * User Roles
		 */
		$db->query(NULL, "CREATE TABLE `roles` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(32) NOT NULL,
		  `description` varchar(255) DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `unq_name` (`name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		$db->query(NULL, "INSERT INTO `roles` (`id`, `name`, `description`)
		VALUES
			(1,'login','Base login with no privileges'),
			(2,'admin','Administrative user, has access to everything.'),
			(3,'member','Member user, has limited access');");

		/**
		 * Users
		 */
		$db->query(NULL, "CREATE TABLE `users` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `email` varchar(127) NOT NULL,
		  `first_name` varchar(150) DEFAULT NULL,
		  `last_name` varchar(150) DEFAULT NULL,
		  `username` varchar(255) NOT NULL,
		  `password` varchar(255) NOT NULL,
		  `logins` int(10) unsigned NOT NULL DEFAULT '0',
		  `last_login` int(10) unsigned NOT NULL DEFAULT '0',
		  `active` tinyint(1) NOT NULL DEFAULT '1',
		  `created` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  `updated` datetime NOT NULL DEFAULT '1001-01-01 00:00:00',
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `unq_email` (`email`),
		  UNIQUE KEY `unq_username` (`username`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		/**
		 * Users/Roles Pivot Table
		 */
		$db->query(NULL, "CREATE TABLE `roles_users` (
		  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `role_id` int(11) unsigned NOT NULL DEFAULT '0',
		  PRIMARY KEY (`user_id`,`role_id`),
		  CONSTRAINT `fk_roles_users_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
		  CONSTRAINT `fk_roles_users_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		/**
		 * Create A Default Test User
		 */
		$user = ORM::factory('User');
		$user->values(array(
			'username' => 'admin',
			'password' => 'westgate',
			'password_confirm' => 'westgate',
			'email' => 'team@ushahidi.com',
			'first_name' => 'David',
			'last_name' => 'Kobia'
		));

		try
		{
			$user->save();

			$user->add('roles', ORM::factory('Role')->where('name', '=', 'login')->find() );
			$user->add('roles', ORM::factory('Role')->where('name', '=', 'admin')->find() );
		}
		catch (ORM_Validation_Exception $e)
		{
			$errors = $e->errors('users');
		}
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "DROP TABLE IF EXISTS `groups`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `messages`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `person_contacts`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `person_statuses`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `people`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `pings`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `pongs`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `providers`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `roles_users`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `roles`;");
		$db->query(NULL, "DROP TABLE IF EXISTS `users`;");
	}

}

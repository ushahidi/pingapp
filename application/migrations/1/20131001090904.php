<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_1_20131001090904 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Add a new 'name' column
		$db->query(NULL, "ALTER TABLE `people` ADD `name` VARCHAR(150)  NULL  DEFAULT NULL  AFTER `user_id`;");

		// Migrate names to new column
		$this->_migrate_old();

		// Drop old columns
		$db->query(NULL, "ALTER TABLE `people` DROP `first_name`;");
		$db->query(NULL, "ALTER TABLE `people` DROP `last_name`;");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		// Restore first_name and last_name
		$db->query(NULL, "ALTER TABLE `people` ADD `first_name` VARCHAR(100)  NULL  DEFAULT NULL  AFTER `user_id`;");
		$db->query(NULL, "ALTER TABLE `people` ADD `last_name` VARCHAR(100)  NULL  DEFAULT NULL  AFTER `first_name`;");

		// Migrate names back to old columns
		$this->_un_migrate_old();

		// Drop name column
		$db->query(NULL, "ALTER TABLE `people` DROP `name`;");
	}


	/**
	 * Migrate Old People
	 * @return void
	 */
	private function _migrate_old()
	{
		$people = ORM::factory('Person')
			->find_all();

		foreach ($people as $person)
		{
			$person->name = $person->first_name.' '.$person->last_name;
			$person->save();
		}
	}

	/**
	 * UnMigrate Old People
	 * @return void
	 */
	private function _un_migrate_old()
	{
		$people = ORM::factory('Person')
			->find_all();

		foreach ($people as $person)
		{
			$name = explode(' ', $person->name);
			$person->first_name = $name[0];
			$person->last_name = (isset($name[1])) ? $name[1] : '';
			$person->save();
		}
	}
}

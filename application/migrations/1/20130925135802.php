<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_1_20130925135802 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// Remove index on contact/type
		$db->query(NULL, 'ALTER TABLE `person_contacts` DROP INDEX `idx_contact_type`;');
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		// Person contacts
		$db->query(NULL, 'ALTER TABLE `person_contacts` ADD UNIQUE INDEX `idx_contact_type` (`contact`, `type`);');
	}

}

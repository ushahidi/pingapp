<?php defined('SYSPATH') OR die('No direct script access.');

class Migration_1_20131014163641 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$this->_migrate_old();

		$db->query(NULL, "ALTER TABLE `contacts` ADD `optin` TINYINT(1)  NOT NULL  DEFAULT '0'  AFTER `type`;");
		$db->query(NULL, "ALTER TABLE `contacts` ADD `unsubscribe` TINYINT(1)  NOT NULL  DEFAULT '0'  AFTER `optin`;");
		$db->query(NULL, "ALTER TABLE `contacts` DROP INDEX `unq_contact_type`;");
		$db->query(NULL, "ALTER TABLE `contacts` ADD UNIQUE INDEX `unq_contact_type` (`contact`, `type`);");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "ALTER TABLE `contacts` DROP `optin`;");
		$db->query(NULL, "ALTER TABLE `contacts` DROP `unsubscribe`;");
		$db->query(NULL, "ALTER TABLE `contacts` DROP INDEX `unq_contact_type`;");
		$db->query(NULL, "ALTER TABLE `contacts` ADD INDEX `unq_contact_type` (`contact`, `type`);");
	}

	/**
	 * Remove Contact Duplicates
	 * @return void
	 */
	private function _migrate_old()
	{
		// Find all contacts with dupes
		$dupes = DB::select('contact')
		    ->from('contacts')
		    ->group_by('contact')
		    ->having(DB::expr('COUNT(id)'), '>', 1);

		$contacts = ORM::factory('Contact')
			->join(array($dupes, 'dupes'), 'INNER')
		    	->on('dupes.contact', '=', 'contact.contact')
		    ->order_by('id', 'ASC')
		    ->find_all();


		$items = array();

		foreach ($contacts as $contact)
		{
			if (in_array($contact->id, $items))
			{
				continue;
			}

			// Find the Dupes
			$dupes = ORM::factory('Contact')
				->where('id', '!=', $contact->id)
				->where('contact', '=', $contact->contact)
				->where('type', '=', $contact->type)
				->find_all();

			foreach ($dupes as $dupe)
			{
				$query = DB::update('pings')
					->set(array('contact_id' => $contact->id))
					->where('contact_id', '=', $dupe->id)
					->execute();

				$query = DB::update('pongs')
					->set(array('contact_id' => $contact->id))
					->where('contact_id', '=', $dupe->id)
					->execute();

				// Add dupe to items to be deleted
				array_push($items, $dupe->id);
			}
		}

		if (count($items))
		{
			$query = DB::delete('contacts')
				->where('id', 'IN', $items)
				->execute();
		}
	}
}

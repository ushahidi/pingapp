<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for the group model
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class GroupModelTest extends Unittest_TestCase {
	/**
	 * Provider for test_validate_valid
	 *
	 * @access public
	 * @return array
	 */
	public function provider_validate_valid()
	{
		return array(
			array(
				// Valid group data
				array(
					'name' => 'Team Ushahidi',
				)
			),
			array(
				// Valid group data
				array(
					'name' => 'Pirates',
				)
			),
			array(
				// Valid group data
				array(
					'name' => 'Ninjas',
				)
			)
		);
	}

	/**
	 * Provider for test_validate_invalid
	 *
	 * @access public
	 * @return array
	 */
	public function provider_validate_invalid()
	{
		return array(
			array(
				// Invalid group data set 1 - No Data
				array()
			),
			array(
				// Invalid group data set 2 - Invalid Name
				array(
					'name' => 'me',
				)
			),
			array(
				// Invalid group data set 3 - Invalid Name
				array(
					'name' => '',
				)
			)
		);
	}

	/**
	 * Test Validate Valid Entries
	 *
	 * @dataProvider provider_validate_valid
	 * @return void
	 */
	public function test_validate_valid($set)
	{
		$group = ORM::factory('Group');
		$group->values($set);

		try
		{
			$group->check();
		}
		catch (ORM_Validation_Exception $e)
		{
			$this->fail('This entry qualifies as invalid when it should be valid: '. json_encode($e->errors('models')));
		}
	}

	/**
	 * Test Validate Invalid Entries
	 *
	 * @dataProvider provider_validate_invalid
	 * @return void
	 */
	public function test_validate_invalid($set)
	{
		$group = ORM::factory('Group');
		$group->values($set);

		try
		{
			$group->check();
		}
		catch (ORM_Validation_Exception $e)
		{
			return;
		}

		$this->fail('This entry qualifies as valid when it should be invalid');
	}
}
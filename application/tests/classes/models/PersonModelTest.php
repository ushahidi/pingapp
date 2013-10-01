<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for the person model
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class PersonModelTest extends Unittest_TestCase {
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
				// Valid person data
				array(
					'name' => 'Joe Schmoe',
					'status' => 'ok',
				)
			),
			array(
				// Valid person data
				array(
					'name' => 'Bill Murray',
					'status' => 'notok',
				)
			),
			array(
				// Valid person data
				array(
					'name' => 'Miley Cyrus',
					'status' => 'unknown',
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
				// Invalid person data set 1 - No Data
				array()
			),
			array(
				// Invalid person data set 2 - Missing Name
				array(
					'status' => 'ok',
				)
			),
			array(
				// Invalid person data set 4 - Invalid status
				array(
					'name' => 'Batcave Chris',
					'status' => 'not okay',
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
		$person = ORM::factory('Person');
		$person->values($set);

		try
		{
			$person->check();
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
		$person = ORM::factory('Person');
		$person->values($set);

		try
		{
			$person->check();
		}
		catch (ORM_Validation_Exception $e)
		{
			return;
		}

		$this->fail('This entry qualifies as valid when it should be invalid');
	}
}
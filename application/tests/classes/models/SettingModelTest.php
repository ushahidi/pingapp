<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for the setting model
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class SettingModelTest extends Unittest_TestCase {
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
				// Valid setting data
				array(
					'key' => 'testkey1',
					'value' => 'value'
				)
			),
			array(
				// Valid setting data with no value
				array(
					'key' => 'testkey2',
				)
			),
			array(
				// Valid setting data
				array(
					'key' => 'testkey3',
					'value' => "\n\n\n
					test data <br />
					test data <br />
					\n\n
					"
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
				// Invalid setting data set 1 - No Data
				array()
			),
			array(
				// Invalid setting data set 2 - Invalid Key
				array(
					'key' => 'me',
				)
			),
			array(
				// Invalid setting data set 3 - Missing Key
				array(
					'value' => 'this is my value',
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
		$setting = ORM::factory('Setting');
		$setting->values($set);

		try
		{
			$setting->check();
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
		$setting = ORM::factory('Setting');
		$setting->values($set);

		try
		{
			$setting->check();
		}
		catch (ORM_Validation_Exception $e)
		{
			return;
		}

		$this->fail('This entry qualifies as valid when it should be invalid');
	}
}
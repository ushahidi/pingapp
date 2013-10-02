<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for the contact model
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class ContactModelTest extends Unittest_TestCase {
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
				// Valid contact data
				array(
					'type' => 'email',
					'contact' => 'joe@schmoe.com',
				)
			),
			array(
				// Valid contact data
				array(
					'type' => 'phone',
					'contact' => '+2541234567',
				)
			),
			array(
				// Valid contact data
				array(
					'type' => 'phone',
					'contact' => '+12039459595',
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
				// Invalid contact data set 1 - No Data
				array()
			),
			array(
				// Invalid contact data set 2 - Invalid Type
				array(
					'type' => 'facebook',
					'contact' => 'ushahidi',
				)
			),
			array(
				// Invalid contact data set 3 - Invalid Email
				array(
					'type' => 'email',
					'contact' => 'test@test',
				)
			),
			array(
				// Invalid contact data set 4 - Invalid Phone
				array(
					'type' => 'phone',
					'contact' => '0000000',
				)
			),
			array(
				// Invalid contact data set 5 - Missing Type
				array(
					'contact' => '+1029349583',
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
		$contact = ORM::factory('Contact');
		$contact->values($set);

		try
		{
			$contact->check();
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
		$contact = ORM::factory('Contact');
		$contact->values($set);

		try
		{
			$contact->check();
		}
		catch (ORM_Validation_Exception $e)
		{
			return;
		}

		$this->fail('This entry qualifies as valid when it should be invalid');
	}
}
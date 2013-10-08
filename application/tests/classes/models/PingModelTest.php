<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for the ping model
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class PingModelTest extends Unittest_TestCase {
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
				// Valid ping data
				array(
					'contact_id' => 1,
					'message_id' => 1,
					'provider' => 'twilio',
					'type' => 'sms',
				)
			),
			array(
				// Valid ping data
				array(
					'contact_id' => 1,
					'message_id' => 1,
					'provider' => 'smssync',
					'type' => 'sms',
				)
			),
			array(
				// Valid ping data
				array(
					'contact_id' => 15,
					'message_id' => 20,
					'type' => 'email',
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
				// Invalid ping data set 1 - No Data
				array()
			),
			array(
				// Invalid ping data set 2 - Invalid Type
				array(
					'contact_id' => 1,
					'message_id' => 1,
					'provider' => 'twilio',
					'type' => 'facebook',
				)
			),
			array(
				// Invalid ping data set 2 - Missing Contact ID
				array(
					'message_id' => 1,
					'provider' => 'twilio',
					'type' => 'sms',
				)
			),
			array(
				// Invalid ping data set 4 - Missing Message ID
				array(
					'contact_id' => 1,
					'provider' => 'twilio',
					'type' => 'sms',
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
		$ping = ORM::factory('Ping');
		$ping->values($set);

		try
		{
			$ping->check();
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
		$ping = ORM::factory('Ping');
		$ping->values($set);

		try
		{
			$ping->check();
		}
		catch (ORM_Validation_Exception $e)
		{
			return;
		}

		$this->fail('This entry qualifies as valid when it should be invalid');
	}
}
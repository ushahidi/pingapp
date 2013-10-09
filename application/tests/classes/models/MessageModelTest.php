<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for the message model
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class MessageModelTest extends Unittest_TestCase {
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
				// Valid message data
				array(
					'title' => 'Are you okay?',
					'message' => 'this is a test message',
					'type' => 'email',
				)
			),
			array(
				// Valid message data
				array(
					'message' => 'Are you okay?',
					'type' => 'sms',
				)
			),
			array(
				// Valid message data
				array(
					'title' => 'Title not needed',
					'message' => 'this is an sms message',
					'type' => 'sms',
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
				// Invalid message data set 1 - No Data
				array()
			),
			array(
				// Invalid message data set 2 - Invalid Type
				array(
					'title' => 'Title not needed',
					'message' => 'this is an sms message',
					'type' => 'facebook',
				)
			),
			array(
				// Invalid message data set 2 - Missing Title in Email
				array(
					'message' => 'this is an email message',
					'type' => 'email',
				)
			),
			array(
				// Invalid message data set 4 - Missing Type
				array(
					'title' => 'Are you okay?',
					'message' => 'this is a test message',
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
		$message = ORM::factory('Message');
		$message->values($set);

		try
		{
			$message->check();
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
		$message = ORM::factory('Message');
		$message->values($set);

		try
		{
			$message->check();
		}
		catch (ORM_Validation_Exception $e)
		{
			return;
		}

		$this->fail('This entry qualifies as valid when it should be invalid');
	}
}
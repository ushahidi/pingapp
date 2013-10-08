<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for the pong model
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class PongModelTest extends Unittest_TestCase {
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
				// Valid pong data
				array(
					'content' => 'I am okay',
					'type' => 'sms',
				)
			),
			array(
				// Valid pong data
				array(
					'content' => '  <strong>I am not okay</strong>',
					'type' => 'email',
				)
			),
			array(
				// Valid pong data
				array(
					'content' => 'okay',
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
				// Invalid pong data set 1 - No Data
				array()
			),
			array(
				// Invalid pong data set 2 - Invalid Type
				array(
					'content' => 'I am okay',
					'type' => 'facebook',
				)
			),
			array(
				// Invalid pong data set 2 - Missing Content
				array(
					'content' => ' ',
					'type' => 'email',
				)
			),
			array(
				// Invalid pong data set 4 - Missing Type
				array(
					'content' => 'I am okay',
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
		$pong = ORM::factory('Pong');
		$pong->values($set);

		try
		{
			$pong->check();
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
		$pong = ORM::factory('Pong');
		$pong->values($set);

		try
		{
			$pong->check();
		}
		catch (ORM_Validation_Exception $e)
		{
			return;
		}

		$this->fail('This entry qualifies as valid when it should be invalid');
	}
}
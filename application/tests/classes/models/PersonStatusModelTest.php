<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for the person_status model
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class PersonStatusModelTest extends Unittest_TestCase {
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
				// Valid person_status data
				array(
					'person_id' => '1',
					'user_id' => '1',
					'status' => 'ok',
					'note' => 'joe\'s sister called and said he\'s okay',
				)
			),
			array(
				// Valid person_status data
				array(
					'person_id' => '10',
					'status' => 'notok',
				)
			),
			array(
				// Valid person_status data
				array(
					'person_id' => '101',
					'user_id' => '1',
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
				// Invalid person_status data set 1 - No Data
				array()
			),
			array(
				// Invalid person_status data set 2 - Missing Person ID
				array(
					'status' => 'ok',
				)
			),
			array(
				// Invalid person_status data set 4 - Invalid status
				array(
					'person_id' => '1',
					'status' => 'not okay',
				)
			),
			array(
				// Invalid person_status data set 4 - Invalid note
				array(
					'person_id' => '1',
					'user_id' => '15',
					'status' => 'notok',
					'note' => 'Tongue jerky strip steak pastrami drumstick cow shoulder hamburger frankfurter. Shank kevin bacon brisket. Hamburger ham shankle flank chicken cow pork loin salami. Pork beef leberkas meatloaf corned beef ground round. Meatloaf biltong turducken, venison shoulder ham bacon tongue hamburger pork loin bresaola. Ham salami meatball, kielbasa boudin rump ribeye turducken. Pancetta brisket shoulder spare ribs, tenderloin kielbasa ribeye andouille ham hock.'
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
		$status = ORM::factory('Person_Status');
		$status->values($set);

		try
		{
			$status->check();
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
		$status = ORM::factory('Person_Status');
		$status->values($set);

		try
		{
			$status->check();
		}
		catch (ORM_Validation_Exception $e)
		{
			return;
		}

		$this->fail('This entry qualifies as valid when it should be invalid');
	}
}
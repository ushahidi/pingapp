<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for plugins
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class PluginTest extends Unittest_TestCase {
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
				// Valid plugin init
				array(
					'twilio' => array(
						'name' => 'Twilio Plugin',
						'version' => '0.1',
						'services' => array(
							'sms' => true,
							'ivr' => true,
							'email' => true
						),
						'options' => array(
							'phone' => 'Phone Number', 
							'account_sid' => 'Account SID',
							'auth_token' => 'Auth Token'
						),
						'links' => array(
							'developer' => 'https://www.twilio.com',
							'signup' => 'https://www.twilio.com/try-twilio'
						)
					)
				)
			),
			array(
				// Valid plugin init
				array(
					'nexmo' => array(
						'name' => 'Nexmo',
						'version' => '0.1',
						'services' => array(
							'sms' => true,
							'ivr' => true,
							'email' => false
						),
						'options' => array(
							'phone' => 'Phone Number', 
							'api_key' => 'API Key',
							'api_secret' => 'API Secret'
						),
						'links' => array(
							'developer' => 'https://www.nexmo.com/',
							'signup' => 'https://dashboard.nexmo.com/register'
						)
					)
				)
			),
			array(
				// Valid plugin init
				array(
					'testme' => array(
						'name' => 'A Fake Plugin',
						'version' => '0.1',
						'services' => array(),
						'options' => array(),
						'links' => array()
					)
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
				// Invalid plugin init (missing version)
				array(
					'microwave' => array(
						'name' => 'Microwave Plugin',
						'services' => array(
							'sms' => true,
							'ivr' => true,
							'email' => true
						),
						'options' => array(),
						'links' => array()
					)
				)
			),
			array(
				// Invalid plugin init (missing options and links)
				array(
					'lightbulb' => array(
						'name' => 'Lightbulb Plugin',
						'version' => '1',
						'services' => array(
							'sms' => true,
							'ivr' => true,
							'email' => true
						),
					)
				)
			),
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
		if ( ! PingApp::valid_plugin($set))
		{
			$this->fail('This entry qualifies as invalid when it should be valid: '. json_encode($set));
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
		if ( ! PingApp::valid_plugin($set))
		{
			return;
		}

		$this->fail('This entry qualifies as valid when it should be invalid');
	}
}
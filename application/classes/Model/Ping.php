<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Pings
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Ping extends ORM {
	/**
	 * A ping belongs to a message, a person 
	 * a person_contact and a provider
	 */
	protected $_belongs_to = array(
		'message' => array(),
		'person' => array(),
			'person_contact' => array(),
		'provider' => array(),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
}
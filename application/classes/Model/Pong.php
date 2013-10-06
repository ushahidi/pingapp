<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Pongs
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Pong extends ORM {
	/**
	 * A pong belongs to a contact and a ping
	 */
	protected $_belongs_to = array(
		'contact' => array(),
		'ping' => array(),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
}
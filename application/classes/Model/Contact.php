<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Contacts
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Contact extends ORM {
	/**
	 * A contact has and belongs to many pings
	 */
	protected $_has_many = array(
		'people' => array('through' => 'contacts_people'),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
	
	public function rules()
	{
		return array(
			'contact' => array(
				array('not_empty'),
				array('min_length', array(':value', 2)),
			)
		);
	}

	/**
	 * Overload saving to perform additional functions
	 */
	public function save(Validation $validation = NULL)
	{
		// Clean up phone numbers
		if ($this->type == 'phone')
		{
			$this->contact = preg_replace("/[^0-9,.]/", "", $this->contact);
		}
		// Use lower case for other contacts for consistency
		else
		{
			$this->contact = strtolower($this->contact);
		}

		parent::save();

		return $this;
	}
}
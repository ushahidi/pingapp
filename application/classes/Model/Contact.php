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
	
	/**
	 * Rules
	 */
	public function rules()
	{
		return array(
			'type' => array(
				array('not_empty'),
				array('in_array', array(':value', array('email', 'phone', 'twitter')) ),
			),
			'contact' => array(
				array('not_empty'),
				array('min_length', array(':value', 2)),
				array(array($this, 'valid_contact'), array(':validation', ':field', 'type')),
			),
		);
	}

	/**
	 * Validate Contact Against Contact Type
	 *
	 * @param array $validation
	 * @param string $field field name
	 * @param [type] [varname] [description]
	 * @return void
	 */
	public function valid_contact($validation, $field)
	{
		// Valid Email?
		if ( isset($validation['type']) AND 
			$validation['type'] == 'email' AND
			 ! Valid::email($validation[$field]) )
		{
			$validation->error($field, 'invalid_email');
		}

		// Valid Phone?
		// ++TODO: There's no easy to validate international numbers
		// so just look for numbers only. A valid international phone
		// number should have atleast 9 digits
		else if ( isset($validation['type']) AND 
			$validation['type'] == 'phone' )
		{
			// Remove all non-digit characters from the number
			$number = preg_replace('/\D+/', '', $validation[$field]);

			if (strlen($number) < 9)
			{
				$validation->error($field, 'invalid_phone');
			}
		}
		else
		{
			if ( ! $validation[$field])
			{
				$validation->error($field, 'invalid_account');
			}
		}
	}

	/**
	 * Finds and returns the Contact record associated with
	 * the specified contact and contact type
	 *
	 * @param string  contact
	 * @param stirng  contact_type
	 *
	 * @return Model_Contact if found, FALSE otherwise
	 */
	public static function get_contact($contact, $contact_type)
	{
		$contact = ORM::factory('Contact')
		    ->where('contact', '=', $contact)
		    ->where('type', '=', $contact_type)
		    ->find();
		
		return $contact->loaded() ? $contact : FALSE;
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
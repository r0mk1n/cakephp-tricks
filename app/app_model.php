<?php
/**
 * Override app_model
 * Added some validation methods, e.g. auto call custom validation
 * @version 1.2
 * @author r0mk1n
 *
 */

class AppModel extends Model {
	var $validationSet = array();

	function __construct( $id = false, $table = null, $ds = null ) {
		parent::__construct( $id, $table, $ds );
		//$this->query( "set names 'utf8'" );
	}

	function setValidation( $setName ) {
		$this->validate = isset( $this->validationSet[$setName] ) ? $this->validationSet[$setName] : null;
	}

/**
 * Set a field as invalid, optionally setting the name of validation
 * rule (in case of multiple validation for field) that was broken
 *
 * @param string $field The name of the field to invalidate
 * @param string $value Name of validation rule that was not met
 * @access public
 */
    function invalidate($field, $value = true) {
        if (!is_array($this->validationErrors)) {
            $this->validationErrors = array();
        }
        if ( isset( $this->validationErrors[$field] ) && strlen( $this->validationErrors[$field] ) && strpos( $this->validationErrors[$field], $value ) === false ) {
        	$this->validationErrors[$field] .= ', ' . $value;
        } else {
        	$this->validationErrors[$field] = $value;
        }
    }

/**
 * Custom validation rules
 *
 */

/**
 * Checking for other field not empty
 */
    function otherFieldNotEmpty( $data, $field_to_check ) {
        return ( !empty( $this->data[$this->name][$field_to_check] ) ) ? true : false;
    }

/**
 * Is this field value equal to another field value?
 */
    function equalToField( $data, $field ) {
        $filed_tmp = array_values( $data );
        return $filed_tmp[0] == $this->data[$this->name][$field];
    }

/**
 * $data array is passed using the form field name as the key
 * have to extract the value to make the function generic
 */
    function alphaNumericDashUnderscore( $data ) {
		$value = array_values($data);
		$value = $value[0];
		return preg_match('|^[0-9a-zA-Z_-]*$|', $value);
    }

    function isValidEmail($email) {
		// First, we check that there's one @ symbol, and that the lengths are right
        if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
			// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
            return false;
        }
		// Split it into sections to make life easier
        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++) {
            if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
                return false;
            }
        }
        if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
            $domain_array = explode(".", $email_array[1]);
            if (sizeof($domain_array) < 2) {
                return false; // Not enough parts to domain
            }
            for ($i = 0; $i < sizeof($domain_array); $i++) {
                if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
                    return false;
                }
            }
        }
        return true;
    }

}
?>

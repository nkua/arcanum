<?php
/**
 * Form functions
 *
 * @package arcanum
 * @version $Id: Form.php 5809 2012-09-20 09:06:12Z avel $
 */

class Arcanum_Form {
    /**
     * User-input value
     */
    protected $value = false;
    
    /**
     * Normalized value from Store (LDAP server etc.)
     */
    protected $normalizedvalue = false;

    public function __construct($value = '') {
        $this->value = $value;
    }

    public function setInputValue($value) {
        $this->value = $value;
        $this->normalizedvalue = $this->normalize($value);
    }
    
    public function setValueFromStore($value) {
        $this->normalizedvalue = $value;
        $this->value = $this->display($value);
    }

    public function getDisplayValue() {
        return $this->value;
    }
    
    public function getNormalizedValue() {
        return $this->normalizedvalue;
    }
    
    public function validate() {
        return true;
    }

    public function normalize($arg) {
        return $arg;
    }

    public function display($arg) {
        return $arg;
    }

    public static function Factory($method) {
        switch($method) {
        case 'sms':
            return new Arcanum_FormSms('');
            break;

        case 'email':
            return new Arcanum_FormEmail('');
            break;

    /*    case 'openid':
            return new Arcanum_FormOpenid('');
            break;
*/

        default:
            throw new Exception('Unknown variable type / method');
        }
    }
}


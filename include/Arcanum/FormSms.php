<?php
/**
 * Form functions
 *
 * @package arcanum
 * @version $Id: FormSms.php 5823 2012-10-02 15:11:31Z avel $
 */

class Arcanum_FormSms extends Arcanum_Form {
    public function __construct($value) {
        parent::__construct($value);
    }

    public function validate() {
        if(empty($this->value)) return true;
        if(preg_match('/^[0-9]{10}$/D', $this->value)) {
            return true;
        }
        return sprintf( _("Wrong mobile phone number. The number must consist of %s digits, e.g. 6931234567"), "10");
    }

    public function normalize($arg) {
        return '+30'.$arg;
    }
    
    public function display($arg) {
        return substr($arg, 3);
    }
}


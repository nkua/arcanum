<?php
/**
 * Form functions
 *
 * @package arcanum
 * @version $Id: FormOpenid.php 5809 2012-09-20 09:06:12Z avel $
 */

class Arcanum_FormOpenid extends Arcanum_Form {
    public function __construct($value) {
        parent::__construct($value);
    }

    public function validate() {
        if(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->value)) {
            return true;
        }
        return _("Invalid OpenID URL");
    }
}


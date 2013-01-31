<?php
/**
 * Form functions
 *
 * @package arcanum
 * @version $Id: FormEmail.php 5823 2012-10-02 15:11:31Z avel $
 */

class Arcanum_FormEmail extends Arcanum_Form {
    public function __construct($value) {
        parent::__construct($value);
    }

    public function validate() {
        global $config;
        if(empty($this->value)) return true;
        if(preg_match("/^[^@]+@[^@]+\.[^@]{2,}$/", $this->value)) {
            if(preg_match("/".$config->institution_domain."/", $this->value)) {
                return sprintf( _("The secondary e-mail address cannot contain the domain name %s, it must be an external / third party e-mail."), $config->institution_domain);
            }
            return true;
        }
        return _("Invalid E-mail Address");
    }
}

<?php
/**
 * @package arcanum
 * @version $Id: Email.php 5823 2012-10-02 15:11:31Z avel $
 */

class Arcanum_Token_Email extends Arcanum_Token {
    /**
     * Lifetime: 1 hour
     */
    const LIFETIME = 3600;

    const PREFIX = 'mailtok';

    /**
     * Initialize a token store with prefix 'email', lifetime 1 hour
     */
    public function __construct() {
        $this->init(self::PREFIX, self::LIFETIME);
    }
    
    public function generate_token() {
        $pad = '';
        for ($i = 0; $i < 64; $i++) {
            $pad .= chr(mt_rand(0,255));
        }
        return md5($pad);
    }
}



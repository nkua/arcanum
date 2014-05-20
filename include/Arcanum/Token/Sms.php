<?php
/**
 * @package arcanum
 * @version $Id: Sms.php 5823 2012-10-02 15:11:31Z avel $
 */

class Arcanum_Token_Sms extends Arcanum_Token {
    /**
     * Lifetime: 10 minutes
     */
    const LIFETIME = 600;
    const PREFIX = 'smstok';

    /**
     * Initialize a token store with prefix 'sms', lifetime 10 mins
     */
    public function __construct() {
        $this->init(self::PREFIX, self::LIFETIME);
    }
    
    /**
     * Generate a number such as 123-456-789-012
     */
    public function generate_token() {
        $parts = array(
            str_pad(mt_rand(0,999), 3, "0", STR_PAD_LEFT),
            str_pad(mt_rand(0,999), 3, "0", STR_PAD_LEFT),
        );
        return $parts[0].$parts[1];
    }
    
    /*
    // FIXME
    public function set_token($data, $mobile, $overwrite = false) {
        if($overwrite || ($hits = $this->store->load($mobile)) === false) {
            $this->store->save($data, $mobile);
        } else {
            return false;
        }
        return true;
    }
     */
}


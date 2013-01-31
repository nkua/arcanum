<?php
/**
 * SHA hash
 *
 * @package change_password
 * @version $Id: HashAlgorithm.SHA.php 5591 2012-03-09 12:47:40Z avel $
 */

/**
 * SHA Class
 */
class HashAlgorithm_SHA {
    public static function Generate($cleartext) {
        return base64_encode(sha1($cleartext, true));
    }
    
    public static function Compare($hash, $cleartext) {
        if(self::Generate($cleartext) == $hash) {
            return true;
        }
        return false;
    }
}


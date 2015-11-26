<?php
/**
 * SSHA hash
 *
 * @package change_password
 * @version $Id: HashAlgorithm.SSHA.php 5591 2012-03-09 12:47:40Z avel $
 */

/**
 * SHA Class
 *
 * @see http://php.net/manual/en/function.sha1.php#52365
 */
class HashAlgorithm_SSHA implements HashAlgorithm {
    
    /** NEW */
    public static function newGenerate($cleartext) {
        mt_srand((double)microtime()*1000000);
        $salt = pack("CCCCCCCC", mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand());
        $hash = base64_encode( sha1( $cleartext . $salt, true) . $salt );
        return $hash;
    }
    
    /** OLD */
    public static function Generate($cleartext) {
        mt_srand((double)microtime()*1000000);
        $salt = pack("CCCC", mt_rand(), mt_rand(), mt_rand(), mt_rand());
        $hash = base64_encode(pack("H*", sha1($cleartext . $salt)) . $salt);
        return $hash;
    }

    public static function Compare($hash, $cleartext) {
        $ohash = base64_decode($hash);
        $osalt = substr($ohash, 20);
        $ohash = substr($ohash, 0, 20);
        $nhash = pack("H*", sha1($cleartext . $osalt));
        if ($ohash == $nhash) {
            return true;
        }
        return false;
    }
}


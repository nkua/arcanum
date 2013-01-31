<?php
/**
 * Unixcrypt
 *
 * @package arcanum
 * @version $Id: HashAlgorithm.Crypt.php 5594 2012-03-13 12:52:13Z avel $
 */

/**
 * UnixCrypt Class
 */
class HashAlgorithm_Crypt implements HashAlgorithm {
    public static function Generate($cleartext) {
        $cset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789./";
        $salt = substr($cset, time() & 63, 1) .
        substr($cset, time()/64 & 63, 1);
        return crypt($cleartext,$salt);
    }
    
    public static function Compare($hash, $cleartext) {
        if(crypt($cleartext, substr($hash,0,2)) == $hash) {
            return true;
        }
        return false;
    }
}

/* vim:set et ts=4: */

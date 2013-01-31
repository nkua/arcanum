<?php
/**
 * DigestHA1
 *
 * @package change_password
 * @version $Id: HashAlgorithm.DigestHA1.php 5594 2012-03-13 12:52:13Z avel $
 */

/**
 * DigestHA1 class
 */
class HashAlgorithm_DigestHA1 {
    public static function Generate($uid,$realm,$cleartext) {
        return md5("${uid}:${realm}:${cleartext}");
    }
}


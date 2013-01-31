<?php
/**
 * Various Utility functions
 *
 * @package arcanum
 * @version $Id: Util.php 5860 2012-10-22 12:47:54Z avel $
 */

/**
 * Various Utility functions
 */
class Arcanum_Util {

    /**
     * @return boolean
     */
    public static function areSecondaryAccountsActive() {
        global $config;
        $test = array_unique(array_values($config->ldap->secondary_accounts->toArray()));
        if(sizeof($test) == 1 && empty($test[0])) {
            return false;
        }
        return true;
    }
}


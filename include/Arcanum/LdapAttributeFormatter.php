<?php
/**
 * @package arcanum
 * @version $Id: LdapAttributeFormatter.php 5896 2012-10-31 13:59:19Z avel $
 */

/**
 * Collection of static methods that display a password-related attribute in a user-friendly way.
 * Methods must accept the empty string as well.
 */
class Arcanum_LdapAttributeFormatter {
    public static function formatLdapPwAccountLockedTime($arg) {
        if($arg == '000001010000Z') {
            return '<span class="warning">' . _("Account has been temporarily locked manually by an administrator, to force change of password.") .'</span>';
        } elseif(!empty($arg)) {
            return self::formatLdapDate($arg);
        } else {
            return _("Account is active.");
        }
    }

    public static function formatLdapDate($arg) {
        if(!empty($arg)) {
            $unixtime = Zend_Ldap_Attribute::convertFromLdapDateTimeValue($arg);
            return strftime("%c", $unixtime);
        } else {
            return _("No dates registered.");
        }
    }
    
    public static function formatLdapPwHistory($arg) {
        if(empty($arg)) {
            return _("Password history is empty.");
        }
        $parts = explode('#', $arg);
        if(sizeof($parts) == 1) {
            // non-standard format. e.g. old SunDS
            return($arg);
        }
        // [0] => date [1] => OID [2] => length [3] => hash ) 
        if(strtolower(substr($parts[3], 0, 6)) == '{ssha}') {
            $myfmt = "SSHA";
        } elseif(strtolower(substr($parts[3], 0, 5)) == '{sha}') {
            $myfmt = "SHA";
        } elseif(strtolower(substr($parts[3], 0, 7)) == '{crypt}') {
            $myfmt = "Crypt";
        }

        if(isset($myfmt)) {
            return sprintf( _("%s-encoded password, set on %s"), $myfmt, self::formatLdapDate($parts[0]));
        } else {
            return sprintf( _("Cleartext password, set on %s"), self::formatLdapDate($parts[0]));
        }
    }

    public static function formatLdapPw($arg) {
        if(empty($arg)) {
            return _("Unreadable or not set.");
        }
        if(strtolower(substr($arg, 0, 6)) == '{ssha}') {
            $myfmt = "SSHA";
        } elseif(strtolower(substr($arg, 0, 5)) == '{sha}') {
            $myfmt = "SHA";
        } elseif(strtolower(substr($arg, 0, 7)) == '{crypt}') {
            $myfmt = "Crypt";
        }

        if(isset($myfmt)) {
            return sprintf( _("%s-encoded password"), $myfmt);
        } else {
            return sprintf(
                '<span rel="tooltip" style="cursor: help;" title="%s" data-placement="right"> ' .
                    _("Cleartext password; hover to reveal") . '</span>',
                htmlspecialchars($arg)
            );
        }
    }
    
    /**
     * $global $policies
     */
    public static function formatPwPolicySubEntry($arg) {
        global $policies;

        if(empty($arg)) {
            return _("Default or unspecified policy.");
        }

        if($policies && $policies['count'] > 0) {
            for($j=0; $j < $policies['count']; $j++) {
                if($arg == $policies[$j]['dn']) {
                    return sprintf(_("Specific Policy: %s"), htmlspecialchars($policies[$j]['cn'][0]));
                }
            }
        }

        return sprintf(_("Specific Policy: %s"), htmlspecialchars($policies[$j]['dn']));
    }

    /**
     * $global $policies
     */
    public static function formatpwdReset($arg) {
        if(!$arg) {
            return _("No");
        }

        return '<br/><span class="alert alert-warning">' . 
            _("Yes; user will be prompted to change it immediately.") .
            '</span>';
    }

}


<?php
/**
 * LDAP Functions.
 *
 * @package arcanum
 * @version $Id: Ldap.php 5966 2013-01-07 09:53:16Z avel $
 */


class Arcanum_Ldap {
    protected $username = '';
    
    /**
     * The LDAP handle used for everything. It should be initialized by binding as manager.
     */
    public $ldap;

    /**
     * The LDAP handle used for user password changes. It should be initialized by binding asmanager.)
     */
    public $ldapU;

    /**
     * An instance of Arcanum_LdapSchema where we'll reference our attributes
     */
    protected $schema;

    /**
     * The constructor merely sets some shorthands for schema attributes
     * @return void
     */
    function __construct() {
        $this->schema = new Arcanum_LdapSchema();
        
        $this->attributes = &$this->schema->attributes;
        $this->pwAttributes = &$this->schema->pwAttributes;
        $this->policyAttributes = &$this->schema->policyAttributes;
    }

    /**
     * @return object ldap handle
     */
    private function _ldap_connect() {
        global $config;
        $ldap = @ldap_connect($config->ldap->host);
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        if($ldap === false) {
            $error_message = ldap_error($this->ldap);
            header("Location: signout.php?forced=3&error_message=".urlencode($error_message));
            exit;
        }
        return $ldap;
    }

    public function connect() {
        global $config;
        $this->ldap = $this->_ldap_connect();

        $bind = @ldap_bind($this->ldap, $config->ldap->bind, $config->ldap->password);
        if($bind === false) {
            $error_message = ldap_error($this->ldap);
            @header("Location: signout.php?forced=3&error_message=".urlencode($error_message));
            exit;
        }

        return @$this->ldap;
    }
    
    /**
     * @param string $uid username
     * @return mixed The requested username's dn, or false if not found
     */
    public function getUserDn($uid) {
        global $config;
        
        $sr = ldap_search($this->ldap, $config->ldap->basedn,
            sprintf($config->ldap->filter->user, self::specialchars($uid)),
            array('uid', 'objectclass', $config->ldap->passwordAttribute));

        if(ldap_count_entries($this->ldap, $sr) != 1 ) {
            $this->_addmsg( _("Duplicate (or no) login entries detected, cannot change password.") );
            return false;
        }

        $info = ldap_get_entries($this->ldap, $sr);
        return $info[0]['dn'];
    }
    
    /**
     * Connect as user. The bind dn must be retrieved beforehand and passed to this method.
     *
     * @param string $dn
     * @param string $password
     * @return boolean
     */
    public function connectAsUser($dn, $password) {
        global $config;
        $this->ldapU = $this->_ldap_connect();
        $bind = @ldap_bind($this->ldapU, $dn, $password);

        if($bind === false) {
            $this->_addmsg( sprintf( _("Could not bind to LDAP server as %s; LDAP error: %s"), $this->username, ldap_error($this->ldapU)) );
            return false;
        }

        return $this->ldapU;
    }

    public function getPolicies() {
        global $config;

        $filter = '(objectclass=pwdpolicy)';

        $sr = ldap_search($this->ldap, $config->ldap->basedn, $filter,
            array_merge(array('cn'), array_keys($this->policyAttributes)) );
        
        if(ldap_count_entries($this->ldap, $sr) == 0) {
            // fallback for SunDS
            $filter = '(&(objectclass=ldapsubentry)(objectclass=pwdpolicy))';
            $sr = ldap_search($this->ldap, $config->ldap->basedn, $filter,
                array_merge(array('cn'), array_keys($this->policyAttributes)) );
        }

        return ldap_get_entries($this->ldap, $sr);

    }

    /**
     * Sanitize an entry array (ldap data).
     * This function will strtolower() all case-insensitive attributes.
     *
     * @param array &$entry
     * @return void
     * @author avel
     */
    public static function sanitize_entry_array(&$entry) {
        /* attributes whose values will be lower-cased: */
        $attrs = array('edupersonorgunitdn', 'edupersonprimaryorgunitdn',
        'uoauserapps', 'edupersonorgdn', 'edupersonprimaryaffiliation',
        'edupersonaffiliation', 'eduorgsuperioruri');

        for($i=0; $i<$entry['count']; $i++) {
            $entry[$i]['dn'] = strtolower($entry[$i]['dn']);
            foreach($attrs as $attr) {
                if(isset($entry[$i][$attr]['count']) && $entry[$i][$attr]['count'] > 0 ) {
                    for($j=0; $j<$entry[$i][$attr]['count']; $j++) {
                        $entry[$i][$attr][$j] = strtolower($entry[$i][$attr][$j]);
                    }
                }
            }
        }
    }

    public static function constructFilterFromQuery($query) {
        global $config;

        $snippet = self::specialchars($query);

        return'(&'.sprintf($config->ldap->filter->user, '*') . '(|' . 
            '(cn=*'.$snippet.'*)'.
            '(uid=*'.$snippet.'*)'.
            '(mail=*'.$snippet.'*)'.
            '))';
    }

    /**
     * Sanitizes ldap search strings.
     * See rfc2254
     * @link http://www.faqs.org/rfcs/rfc2254.html
     * @param string $string
     * @return string sanitized string
     */ 
    public static function specialchars($string) {
        $sanitized=array(
            '\\' => '\5c',
            '*' => '\2a',
            '(' => '\28',
            ')' => '\29',
            "\x00" => '\00');
        
        return str_replace(array_keys($sanitized),array_values($sanitized),$string);
    }
    
    public static function getAttributeDesc($attr) {
        return $this->attributes[$attr]['desc'];
    }

    protected function _addmsg($message, $class = 'error', $param = 'generic') {
        $this->msgs[] = array(
            'param' => $param,
            'msg' => $message,
            'class' => $class
        );
    }

    public function getMsgs() {
        return $this->msgs;
    }
}

/*

class Aranum_Ldap_Factory {
    const 'USER_BIND' = 1;
    const 'ADMIN_BIND' = 2;

    public static function createLdapConn($type = ''){
        switch($type) {
        case self::USER_BIND:
            return new();
            break;
        
        case self::ADMIN_BIND:
        default:
            return new();
            break;
        }
    }
}
 */


/**
 * Convert character set of a string.
 * @param string $string String to convert.
 * @param string $from_charset Original charset.
 * @param string $to_charset Destination charset.
 * @return string Converted string.
 */
function directory_string_convert($string, $from_charset, $to_charset) {
    
    if(strcasecmp($from_charset, $to_charset) == 0 ) {
        return $string;
    }

    if(function_exists("mb_convert_encoding")) {
        return mb_convert_encoding($string, $to_charset, $from_charset);

    } elseif(function_exists("recode_string")) {
        return recode_string("$from_charset..$to_charset", $string);
    
    } elseif(function_exists("iconv")) {
        return iconv($from_charset, $to_charset, $string);

    } else {
        return 0;
    }
}    


/**
 * Sanitizes ldap search strings (alias)
 */ 
function ldapspecialchars($string) {
    return Arcanum_Ldap::specialchars($string);
}


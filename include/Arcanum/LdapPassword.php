<?php
/**
 * Change Password LDAP backend code.
 *
 * @package arcanum
 * @version $Id: LdapPassword.php 5864 2012-10-24 12:52:43Z avel $
 */

/**
 * Include Password hash functionality
 */
include_once('include/HashAlgorithm.php');
include_once('include/HashAlgorithm.Crypt.php');
include_once('include/HashAlgorithm.DigestHA1.php');
include_once('include/HashAlgorithm.SHA.php');
include_once('include/HashAlgorithm.SSHA.php');
include_once('include/HashAlgorithm.NTHash.php');
include_once('Crypt/TripleDES.php');

/**
 * ChangePassword Backend for changing Password on LDAP Database.
 */
class Arcanum_LdapPassword extends Arcanum_Ldap {
    private $valid_params = array('username', 'dn', 'proxy', 'force_change', 'enable_strength_check', 'oldpass', 'newpass', 'verify');

    /** A flag to specify an operation to be performed as an LDAP user */
    const AS_USER = 1;

    /** A flag to specify an operation to be performed as an LDAP administrator */
    const AS_ADMIN = 2;

    protected $proxy = '';
    protected $force_change = '';
    protected $enable_strength_check = true;
    protected $oldpass = '';
    protected $newpass = '';
    protected $verify = '';

    protected $dn = '';
    
    protected $passwordAttribute = 'userpassword';

    /** Messages */
    protected $msgs = array();

    /**
     * Initialization of environment -- connect to ldap etc.
     */
    public function __construct() {
        global $config;
        parent::__construct();

        if(isset($config->ldap->passwordAttribute)) {
            $this->passwordAttribute = strtolower($config->ldap->passwordAttribute);
        }
    }

    public function setParameters($arr) {
        foreach($arr as $key=>$val) {
            if(in_array($key, $this->valid_params)) {
                $this->$key = $val;
            }
        }
    }
    
    /**
     * Validate parameters
     * 
     * @return void Returns nothing; must check result of getMsgs();
     */
    public function validateParameters($strength_check = true) {
        global $config;

        if(empty($this->username)) {
            $this->_addmsg(_('Empty username.'));
            break;
        } else {
        
            if(empty($this->dn)) {
                $userdn = $this->getUserDn($this->username);
                if($userdn === false) {
                    $this->_addmsg(_("Could not resolve username to an entry in LDAP directory."));
                } else {
                    $this->dn = $userdn;
                }
            }
        }
    
        $this->validatePassword();
    }

    public function validatePassword($check_strength = true) {
        global $config;

        // oldpass
        if(empty($this->oldpass) && $this->proxy === false) {
            $this->_addmsg( _("You must type in your old password.") );
        }

        // newpass
        if(empty($this->newpass)) {
            $this->_addmsg( _("You must type in your new password.") );
            return;
        }

        if(preg_match('/^[!-~]*$/u', $this->newpass) == 0) {
            $this->_addmsg( dgettext('password_strength_check', "The password contains invalid characters."));
        }

        if($this->force_change && $this->oldpass == $this->newpass) {
            $this->_addmsg(_("The new password is the same as your previous password. You are required to change the password."));
        }
        
        // verify
        if($this->verify != $this->newpass) {
            $this->_addmsg( _("Your new password does not match the verify password.") . 
                ( (strtolower($this->newpass) == strtolower($this->verify) ) ? 
                  ' ' . _("Make sure you use the same case in both input boxes.") : '') );
        }

        if($check_strength && $this->enable_strength_check === true) {
            $check = new passwordStrengthCheck($config->password_strength_policy->toArray());
            $ret = $check->runTests(array($this->username, $this->newpass), true);

            if(sizeof($ret) > 0) {
                foreach($ret as $msg) {
                    $this->_addmsg($msg, 'warning');
                }
            }
        }
    }

    /**
     * LDAP-Related Checks
     *
     * @return boolean
     */
    public function check() {
        /*
        if(!$this->proxymode) {
            // Check if existing LDAP password is the same. This means
            // comparing provided old password, $this->parent->oldpass, to the
            // existing hashed password in LDAP, $ldap_password.

            $ldap_password = $info[0][$this->passwordAttribute][0];

            if(strtoupper(substr($ldap_password, 0, 7)) == '{CRYPT}') {
                $authenticated = HashAlgorithm_Crypt::Compare(substr($ldap_password, 7), $this->parent->oldpass);
            
            } elseif(strtoupper(substr($ldap_password, 0, 5)) == '{SHA}') {
                $authenticated = HashAlgorithm_SHA::Compare(substr($ldap_password, 5), $this->parent->oldpass);

            } elseif(strtoupper(substr($ldap_password, 0, 6)) == '{SSHA}') {
                $authenticated = HashAlgorithm_SSHA::Compare(substr($ldap_password, 6), $this->parent->oldpass);

            } else {
                // Assume cleartext otherwise
                if($ldap_password == $this->parent->oldpass) {
                    $authenticated = true;
                } 
            }

            if(!$authenticated) {
                $this->_addMsg( _("Your old password is not correct.") );
                return false;
            }
        }
         */
        return true;
    }

    /**
     * Enables a user to change her password. Essentially sets pwdReset attribute to TRUE
     */
    public function allowUserPasswordReset() {
        global $config;
        $newinfo = array('pwdReset' => 'TRUE');
        if (@ldap_modify($this->ldap,$this->dn,$newinfo)) {
            return true;
        } else {
            $this->_addMsg( sprintf( _("Could not set pwdReset flag: %s"), ldap_error($this->ldap)) );
            return false;
        }
    }

    /**
     * Generate password Attribute value
     * @param string $pass
     * @return array For passing to ldap_modify()
     */
    private function _generatePasswordHash($password) {
        global $config;
        $newinfo = array();
        if($config->ldap->passwordHash == 'sha') {
            // SHA hash
            $newinfo[$this->passwordAttribute]="{SHA}".HashAlgorithm_SHA::Generate($password);
        } elseif($config->ldap->passwordHash == 'ssha') {
            // SHA hash
            $newinfo[$this->passwordAttribute]="{SSHA}".HashAlgorithm_SSHA::Generate($password);
        } else {
            // Unix Crypt
            $newinfo[$this->passwordAttribute]="{CRYPT}".HashAlgorithm_Crypt::Generate($password);
        }
        return $newinfo;
    }

    /**
     * Perform update on LDAP Database.
     *
     * @param integer $as Change password as user or admin. If changing password as user,
     *                the password policy will be enforced.
     * @return boolean
     */
    public function changeUserPassword($as = false) {
        global $config;

        if($as === false) $as = self::AS_USER;

        $newinfo = array();

        // We use cleartext password so that the policy will be applied.
        // alternative would be:
        // $newinfo = $this->_generatePasswordHash($this->newpass);
        $newinfo[$this->passwordAttribute] = $this->newpass;
        
        if($as === self::AS_USER) {
            $ldaphandle = $this->ldapU;
        } elseif($as === self::AS_ADMIN) {
            $ldaphandle = $this->ldap;
        }

        if (@ldap_modify($ldaphandle, $this->dn, $newinfo)) {
            Arcanum_Logger::log_user('changepass,' . ($as === self::AS_USER ? 'as_user' : 'as_admin') . ',uid='.$this->username);
            return true;
        } else {
            if(ldap_errno($ldaphandle) == 19) {
                // constraint violation
                // we can't get the passwordPolicy.error control message so we say what might be the problem.
                // the possible problems are:
                // "Too early to Update" - passwordTooYoung(7)
                // "Passsword Quality" - insufficientPasswordQuality(5) or passwordTooShort(6) <- unlikely in arcanum
                // "Invalid Reuse" - passwordInHistory(8)
                $this->_addMsg( sprintf( _("Password change failed. You have probably already used this same password recently, or it is still too early to change the password again. Try entering a different password."), ldap_error($this->ldapU)) );
            } else {
                // generic error
                $this->_addMsg( sprintf( _("Password change failed: %s"), ldap_error($this->ldapU)) );
            }
            return false;
        }
    }

    public function changeUserAdditionalPasswordAttributes() {
        global $config;

        if (empty($config->ldap->sambaNtAttribute)  &&
            empty($config->ldap->digestha1Attribute) &&
            empty($config->ldap->ctpAttribute) &&
            $config->institution_domain != 'uoa.gr'
            ) {
            // we have nothing additional to change, report success
            return true;
        }
        // Samba Hash
        if(!empty($config->ldap->sambaNtAttribute)) {
            $newinfo[$config->ldap->sambaNtAttribute] = HashAlgorithm_NTHash::Generate($this->newpass);
        }

        // DigestHA1
        if(!empty($config->ldap->digestha1Attribute)) {
            $newinfo[$config->ldap->digestha1Attribute] = HashAlgorithm_DigestHA1::Generate($this->username, $config->ldap->digestRealm, $this->newpass);
        }
        
        // CTP-uoa
        if($config->institution_domain == 'uoa.gr') {
            include_once('include/uoa/uoactp.php');
            $newinfo['uoactp'] = uoaenc($this->username, $this->newpass);
        }

        // CTP
        if(!empty($config->ldap->ctpAttribute)) {
            $newinfo[$config->ldap->ctpAttribute] = $this->_encode_ctp($this->newpass);
        }

        if (ldap_modify($this->ldap, $this->dn, $newinfo)) {
            return true;
        } else {
            $this->_addMsg( sprintf( _("Password change failed: %s"), ldap_error($this->ldap)) );
            return false;
        }
    }

    /**
     * Retrieve the cleartext password based on an ldap object
     *
     * @return mixed String of the cleartext password, or boolean false if it cannot be determined.
     */
    public function getCleartextPassword(&$info) {
        global $config;

        $ldap_password = $info[$this->passwordAttribute][0];

        $hashed = false;
        if(strtoupper(substr($ldap_password, 0, 7)) == '{CRYPT}' ||
           strtoupper(substr($ldap_password, 0, 5)) == '{SHA}' ||
           strtoupper(substr($ldap_password, 0, 6)) == '{SSHA}') {
               $hashed = true;
        } else {
            if(empty($ldap_password)) {
                return false;
            }
            // cleartext already
            return $ldap_password;
        }
        
        if($config->institution_domain == 'uoa.gr') {
            include_once('include/uoa/uoactp.php');
            if(isset($info['uoactp']) && !empty($info['uoactp'][0])) {
                return uoadec($info['uid'][0], $info['uoactp'][0]);
            }
        }
            
        if(!empty($config->ldap->ctpAttribute) &&
            isset($info[$config->ldap->ctpAttribute]) &&
            !empty($info[$config->ldap->ctpAttribute][0])
        ) {

            return $this->_decode_ctp($info[$config->ldap->ctpAttribute][0]);
        }

        return false;
    }

    public static function getChangedTime(&$entry) {
        if(isset($entry['pwdchangedtime'])) {
            $changedtime = Zend_Ldap_Attribute::convertFromLdapDateTimeValue($entry['pwdchangedtime'][0]);
        } elseif(isset($entry['pwdChangedTime'])) {
            $changedtime = Zend_Ldap_Attribute::convertFromLdapDateTimeValue($entry['pwdChangedTime'][0]);
        } elseif(isset($entry['modifyTimestamp'])) {
            $changedtime = Zend_Ldap_Attribute::convertFromLdapDateTimeValue($entry['modifyTimestamp'][0]);
        } else {
            $changedtime = Zend_Ldap_Attribute::convertFromLdapDateTimeValue($entry['modifytimestamp'][0]);
        }
        return $changedtime;
    }
    
    /**
     * Given an LDAP entry, find out how much more this password has to expire.
     *
     * @param object $entry
     * @return integer Seconds to expiry
     * @todo
     */
    public static function getTimeToExpire(&$entry, $pwdmaxage) {
        global $currenttime;
        if(!isset($currenttime)) $currenttime = time();

        $age = $currenttime - self::getChangedTime($entry);
        
    }
    
    /* ===== Utility functions follow ===== */

    /**
     * Unix time to generalizedTime, but don't take into account the hour.
     *
     * @param int $timestamp
     * @return string
     */
    function _unixdate_to_generalizedtime($timestamp = '') {
        if(empty($timestamp)) {
            $timestamp = time();
        }
        $hour = '000000';
        return date("Ymd", $timestamp) . $hour . "Z";
    }
    
    /**
     * Unix time to precise generalizedTime -- do take into account the hour of
     * day.
     *
     * @param int $timestamp
     * @return string
     */
    function _unixtime_to_generalizedtime($timestamp = '') {
        if(empty($timestamp)) {
            $timestamp = time();
        }
        $hour = date('Hi', $timestamp);
        return date("Ymd", $timestamp) . $hour . "00Z";
    }

    private function _init_3des() {
        global $config;
        $des = new Crypt_TripleDES(CRYPT_DES_MODE_CBC);
        $des->setKey(base64_decode($config->ldap->ctpKey));
        return $des;
    }

    private function _encode_ctp($clear) {
        $des = $this->_init_3des();
        return base64_encode($des->encrypt($clear));
    }

    private function _decode_ctp($encoded) {
        $des = $this->_init_3des();
        return $des->decrypt(base64_decode($encoded));
    }
    
    /**
     * Public interface to _encode_ctp()
     *
     * @param string $clear
     * @return string
     */
    public function getCTP($clear) {
        return $this->_encode_ctp($clear);
    }

}


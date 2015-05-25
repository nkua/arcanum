<?php
/**
 * Arcanum Session Functions and wrappers.
 *
 * @package arcanum
 * @version $Id: Session.php 5962 2013-01-04 14:03:21Z avel $
 */

/**
 * Session & authentication functions
 */
class Arcanum_Session {
    /** Session timeout*/
    const LOGOUT_REASON_SESSION_TIMEOUT = 1;

    /** Login / authentication error */
    const LOGOUT_REASON_WRONG_LOGIN = 2;

    /** System / Server / Admin error */
    const LOGOUT_REASON_SERVER_ERROR = 3;

    /** wrong token for forgotten password reset */
    const LOGOUT_REASON_WRONG_TOKEN = 4;

    /** access denied */
    const LOGOUT_REASON_ACCESS_DENIED = 5;

    /** Login / authentication error because of locked account */
    const LOGOUT_REASON_ACCOUNT_LOCKED = 6;

    /** Not logged in via CAS */
    const LOGOUT_REASON_CAS_NOT_AUTHENTICATED = 7;

    /**
     * Start a session for the very first time, just when a user has been confirmed
     */
    public static function init() {
        global $config, $loggedin;
        $loggedin = false;

        session_name($config->session_name);
        //session_start();
        @session_destroy();
        session_start();
        session_regenerate_id(true);
    }

    /**
     * Start a session
     */
    public static function start() {
        global $config, $loggedin;
        $loggedin = false;

        session_name($config->session_name);
        session_start();
    }

    public static function check($force = true) {
        global $config, $initLocation, $loggedin, $login_username, $cpAuthKey;

        // FIXME COOKIE['key']
        if(!isset($_SESSION['authenticated']) || !isset($_SESSION['login_username'])) {
            // Not logged in
            if($force) {
                self::logout(self::LOGOUT_REASON_SESSION_TIMEOUT);
                exit;
            } else {
                return false;
            }
        }
        
        // Set up some global variables to be available
        $login_username = $_SESSION['login_username'];
        if(isset($_COOKIE['key'])) {
            $cpAuthKey = $_COOKIE['key'];
        }
        $loggedin = true;
        return true;
    }
    
    /**
     * Only to be called from setup.php: Checks if there is a configuration
     * file. If there is one, then proceed with normal session / authentication
     * checks.
     *
     * @return boolean
     */
    public static function check_setup() {
        global $config, $initLocation, $loggedin, $login_username, $cpAuthKey;

        if(
            $initLocation != 'setup' || 
            file_exists('config/config.php')
        ) {
            return self::check(true);
        }

        if(file_exists('config/web_installer_disabled.php')) {
            self::logout(self::LOGOUT_REASON_ACCESS_DENIED);
        }

        return true;
        
    }

    public static function authenticate_cas() {
        global $config, $initLocation, $loggedin, $login_username, $cpAuthKey;
        require_once('CAS/CAS.php');
    	//phpCAS::setDebug('/tmp/arcanum.log');
        phpCAS::client(CAS_VERSION_2_0, $config->cas->host, $config->cas->port, $config->cas->uri, true);
        //phpCAS::setCasServerCACert('lib/CAS/HARICA.pem');
    	phpCAS::setNoCasServerValidation(); 
        phpCAS::handleLogoutRequests();

        if(phpCAS::checkAuthentication()) {
            if (isset($_GET['logout'])) {
                // this should be removed...
                phpCAS::logout();
                exit;
            }
            $casdata = $_SESSION['phpCAS'];
            
//            self::init();

            $login_username = $_SESSION['login_username'] = $casdata['user'];
            $_SESSION['cleared_for'] = array('dataentry', 'passwordreset');
            $_SESSION['ask_old_password'] = true;
            $_SESSION['authenticated'] = true;
            $_SESSION['authenticated_via_cas'] = true;

            if(isset($_GET['service'])) {
                $_SESSION['service'] = $service = urldecode($_GET['service']);
            }

            $loggedin = true;
            return true;
            
        } else {
            return false;
        }
    }
    
    public static function getRole() {
        global $roles, $config, $initLocation;
        if(!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] === false) {
            if(empty($config->ldap->host) && $initLocation == 'setup') {
                return $roles['installer'];
            }
            return $roles['anonymous'];
        }
        if(isset($_SESSION['isAdmin']) && isset($_SESSION['role']) && isset($roles[$_SESSION['role']])) {
            return $roles[$_SESSION['role']];
        } else {
            return $roles['user'];
        }
    }
    
    /**
     * Fetch restrict filter if exists. Only for password_admin role.
     *
     * @return mixed boolean false if there is no restrict filter, or an array of
     *    restrict filter information
     */
    public static function getRestrict() {
        global $config;
        if(!isset($_SESSION['restrict'])) {
            return false;
        }
        if($config->institution_domain == 'uoa.gr') {
            return $_SESSION['restrict'];
        } else {
            $idx = $_SESSION['restrict'];
            $tmp = $config->ldap->restrictfilters->toArray();
            if(!isset($tmp[$idx])) {
                throw new Arcanum_Session_InvalidRestrictException('Invalid Restrict Filter in session');
            }
            return $tmp[$idx];
        }
    }
    
    public static function authenticate_token($token) {
        global $config, $initLocation, $loggedin, $login_username, $cpAuthKey;

        if(strlen($token) == 6) {
            // numeric 6-digit token
            $tok = new Arcanum_Token_Sms();
            $envstorecheck = true;

        } elseif(strlen($token) == 32) {
            // e-mail token
            $tok = new Arcanum_Token_Email();
            $envstorecheck = false;

        } else {
            sleep(4); self::logout(self::LOGOUT_REASON_WRONG_TOKEN);
        }
        
        $uid = $tok->get_token($token);

        if($uid === false) {
            sleep(4); self::logout(self::LOGOUT_REASON_WRONG_TOKEN);
        }
        
        if($envstorecheck) {
            $envStore = new Arcanum_EnvironmentStore();
            $initiated_reset_pw = $envStore->get('initiated_reset_pw');
            $confirmed_reset_pw = $envStore->get('confirmed_reset_pw');

            if($initiated_reset_pw === false || !isset($initiated_reset_pw[$uid]) ||
               $confirmed_reset_pw === false || !isset($confirmed_reset_pw[$uid])) {
                sleep(4); self::logout(self::LOGOUT_REASON_WRONG_TOKEN);
            }

            unset($initiated_reset_pw[$uid]);
            unset($confirmed_reset_pw[$uid]);
            $envStore->set('initiated_reset_pw', $initiated_reset_pw);
            $envStore->set('confirmed_reset_pw', $confirmed_reset_pw);
        
        }

        // auth successful

        $tok->delete_token($token);

        self::init();

        $_SESSION['login_username'] = $uid;
        $_SESSION['reset_forgotten_password_enabled'] = true;
        $_SESSION['cleared_for'] = array('passwordreset');
        $_SESSION['authenticated'] = true;

        $loggedin = true;
        return true;
    }

    /**
     * Redirect to logout page
     * @param integer $forcelevel Reason for logout.
     * @param string $msg Optional message to pass along in the URL, it will be displayed in signout page.
     */
    public static function logout($forcelevel = -1, $msg = '') {
        if($forcelevel == -1) {
            $forcelevel = self::LOGOUT_REASON_SESSION_TIMEOUT;
        }

        // pass along any flags that we have detected. These are informational only.
        $flags = self::_getRequestFlags();

        header(
            "Location: signout.php?forced=".$forcelevel.
            (!empty($msg) ? '&error_message='.urlencode($msg) : '') .
            (!empty($flags) ? '&' . implode('&', $flags) : '')
        );
        exit;
    }

    /**
     * Logout and immediately redirect to login page
     *
     * @param integer $forcelevel Reason for logout.
     * @param string $msg Optional message to pass along in the URL, it will be displayed in signout page.
     */
    public static function logoutToLogin($forcelevel = -1, $msg = '') {
        // pass along any flags that we have detected. These are informational only.
        $flags = self::_getRequestFlags();

        self::destroy();

        header(
            "Location: index.php?displaymsg=1&forced=" . $forcelevel .
            (!empty($msg) ? '&error_message='.urlencode($msg) : '') .
            (!empty($flags) ? '&' . implode('&', $flags) : '')
        );
        exit;
    }
    
    /**
     * Deletes an existing session, more advanced than the standard PHP
     * session_destroy(), it explicitly deletes the cookies and global vars.
     */
    public static function destroy() {
        global $baseuri, $_COOKIE, $_SESSION;

        if (isset($_COOKIE[session_name()]) && session_name()) Arcanum_Security::setCookie(session_name(), '', 0, $baseuri);
        if (isset($_COOKIE['username']) && $_COOKIE['username']) Arcanum_Security::setCookie('username','',0,$baseuri);
        if (isset($_COOKIE['key']) && $_COOKIE['key']) Arcanum_Security::setCookie('key','',0,$baseuri);

        $sessid = session_id();
        if (!empty( $sessid )) {
            $_SESSION = array();
            session_destroy();
            session_write_close();
        }
    }

    public static function _getRequestFlags() {
        $flags = array();

        if(isset($_REQUEST['service'])) {
            $flags[] = 'service='.rawurlencode(urldecode($_REQUEST['service']));

        }
        if(isset($_REQUEST['expired'])) {
            $flags[] = 'expired=1';
        }
        if(isset($_REQUEST['resetted'])) {
            $flags[] = 'resetted=1';
        }
        return $flags;
    }

    public static function getLogoutReasonMessage($forced) {
        switch($forced) {
                
        case Arcanum_Session::LOGOUT_REASON_SESSION_TIMEOUT:
            return _("Session has timed out.");
            break;
            
        case Arcanum_Session::LOGOUT_REASON_WRONG_LOGIN:
            return _("Username or password is incorrect.");
            break;

        case Arcanum_Session::LOGOUT_REASON_SERVER_ERROR:
            return _("There was a server error in the password management service.");
            break;

        case Arcanum_Session::LOGOUT_REASON_WRONG_TOKEN:
            return _("The password recovery token was incorrect.");
            break;

        case Arcanum_Session::LOGOUT_REASON_ACCESS_DENIED:
            return _("Access denied.");
            break;

        case Arcanum_Session::LOGOUT_REASON_ACCOUNT_LOCKED:
            return _("Account is locked by an administrator.");
            break;
            
        case Arcanum_Session::LOGOUT_REASON_CAS_NOT_AUTHENTICATED:
            return _("You have not logged in the central authentication service.");
            break;
        }
        return '';

    }
}

class Arcanum_Session_InvalidRestrictException extends Exception {}

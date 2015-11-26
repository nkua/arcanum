<?php
/**
 * Change Password Feature.
 *
 * @package arcanum
 * @version $Id: redirect.php 6014 2013-01-29 12:53:59Z avel $
 */

$initLocation = 'redirect';
require_once('include/init.php');

if(!isset($_POST['login_username'])) {
    header("Location: index.php");
    exit;
}

$login_username = $_POST['login_username'];
$password = isset($_POST['password']) ? $_POST['password'] : '';

if(empty($password)) {
    header("Location: index.php");
    exit;
}

require_once('include/LoginProtector.php');

// 0) check captcha, if it is required
if(!empty($config->recaptcha->pubkey)) {
    $loginProtector = new LoginProtector();
    $loginProtector->increment_tries();
    if($loginProtector->get_tries() > 5) {
        // captcha required here.
        if(empty($_POST['recaptcha_challenge_field']) || empty($_POST['recaptcha_response_field'])) {
            Arcanum_Session::logoutToLogin(Arcanum_Session::LOGOUT_REASON_WRONG_LOGIN, _("Please type in the two scrambled words in order to login."));
        }

        include_once('Zend/Captcha/ReCaptcha.php');
        $recaptcha = new Zend_Service_ReCaptcha($config->recaptcha->pubkey, $config->recaptcha->privkey);
        $captcha_result = $recaptcha->verify(
            $_POST['recaptcha_challenge_field'],
            $_POST['recaptcha_response_field']
        );
        if (!$captcha_result->isValid() && !$config->devel->allow_all_captcha) {
            Arcanum_Session::logoutToLogin(Arcanum_Session::LOGOUT_REASON_WRONG_LOGIN, _("You did not enter the two scrambled words correctly."));
        }
    }
}

$arcanumLdap = new Arcanum_Ldap();
$ldap = $arcanumLdap->connect();

$adminLogin = false;

// 1) Check with admin filters first
$filter = $config->ldap->filter->admin_policy;
$sr = @ldap_search($ldap, $config->ldap->basedn, sprintf($filter, ldapspecialchars($login_username)), array('uid', 'userpassword'));
if($sr === false) Arcanum_Session::logoutToLogin(Arcanum_Session::LOGOUT_REASON_WRONG_LOGIN, ldap_error($ldap));

if(ldap_count_entries($ldap, $sr) == 1) {
    $adminLogin = true;
    $role = 'admin_policy';
    Arcanum_Logger::log_user('admin_policy_login,uid='.$login_username);
}

if(!$adminLogin) {
    $filter = $config->ldap->filter->admin_password;
    $sr = @ldap_search($ldap, $config->ldap->basedn, sprintf($filter, ldapspecialchars($login_username)), array('uid', 'userpassword', 'uoauserappschangepass'));
    if($sr === false) Arcanum_Session::logoutToLogin(Arcanum_Session::LOGOUT_REASON_WRONG_LOGIN, ldap_error($ldap));
    if(ldap_count_entries($ldap, $sr) == 1) {
        $adminLogin = true;
        $role = 'admin_password';
        Arcanum_Logger::log_user('admin_password_login,uid='.$login_username);

        // Also check against restrictive filters if this password admin only has access
        // to certain users in the directory

        if(isset($config->ldap->restrictfilters)) {
            foreach($config->ldap->restrictfilters->toArray() as $k => $restrict) {
                $sr2 = @ldap_search($ldap, $config->ldap->basedn, 
                    '(&'.sprintf($config->ldap->filter->admin_password, ldapspecialchars($login_username)) . $restrict['adminfilter'] . ')',
                    array('uid', 'userpassword'));

                if(ldap_count_entries($ldap, $sr2) == 1) {
                    $set_restrict = $k;
                }
                unset($sr2);
            }
        }

    }
}

if(!$adminLogin) {
    // 2) Search with user filter

    $filter = $config->ldap->filter->user;
    $sr = @ldap_search($ldap, $config->ldap->basedn, sprintf($filter, ldapspecialchars($login_username)), array('uid', 'userpassword', 'pwdreset', 'pwdaccountlockedtime'));
    
    if(ldap_count_entries($ldap, $sr) !== 1) {
        Arcanum_Logger::log_user('loginfail_uidnotfound,uid='.$login_username);
        Arcanum_Session::logoutToLogin(Arcanum_Session::LOGOUT_REASON_WRONG_LOGIN);
    }
    $role = 'user';
}

// By now we should have an ldap search results resource $sr with one match

$info = ldap_get_entries($ldap, $sr);
Arcanum_Ldap::sanitize_entry_array($info);

$authenticated = false;

    /*
    // This snippet is for authenticating via an LDAP BIND. It currently does not work for PHP.

    $bind = @ldap_bind($ldap, $info[0]['dn'], $password);
    ldap_unbind($ldap);
    
    $ldap = $arcanumLdap->connect();
    
    $ctrl = array("oid" => "1.3.6.1.4.1.42.2.27.8.5.1", "iscritical" => true);

    if (!ldap_set_option($ldap, LDAP_OPT_SERVER_CONTROLS, array($ctrl))) {
        echo "Failed to set server controls";
    }

    define('LDAP_OPT_DIAGNOSTIC_MESSAGE', 0x0032);

    if (ldap_get_option($ldap, LDAP_OPT_SERVER_CONTROLS, $ret)) {
        print_r($ret);
        print '<br>';
    }else {
        echo " ?! ";
    }
    if (ldap_get_option($ldap, LDAP_OPT_CLIENT_CONTROLS, $ret)) {
        print_r($ret);
        print '<br>';
    }else {
        echo " ?! ";
    }

     if (ldap_get_option($ldap, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error)) {
         //echo "Extended error: $extended_error";
     } else {
         //echo "No additional information is available.";
     }
    if($bind === true) {
        $authenticated = true;
    }
    */

include_once('include/HashAlgorithm.php');
include_once('include/HashAlgorithm.Crypt.php');
include_once('include/HashAlgorithm.SHA.php');
include_once('include/HashAlgorithm.SSHA.php');

$ldap_password = $info[0]['userpassword'][0];

$authenticated = false;

if(strtoupper(substr($ldap_password, 0, 7)) == '{CRYPT}') {
    $authenticated = HashAlgorithm_Crypt::Compare(substr($ldap_password, 7), $password);

} elseif(strtoupper(substr($ldap_password, 0, 5)) == '{SHA}') {
    $authenticated = HashAlgorithm_SHA::Compare(substr($ldap_password, 5), $password);

} elseif(strtoupper(substr($ldap_password, 0, 6)) == '{SSHA}') {
    $authenticated = HashAlgorithm_SSHA::Compare(substr($ldap_password, 6), $password);

} else {
    if($ldap_password === $password) {
        $authenticated = true;
    }
}


if($authenticated) {
    // If the account has been locked, inform the user and logout
    if(!empty($info[0]['pwdaccountlockedtime']) && !empty($info[0]['pwdaccountlockedtime'][0])) {
        $authenticated = false;
        Arcanum_Logger::log_user('loginfail_locked,uid='.$login_username);
        Arcanum_Session::logoutToLogin(Arcanum_Session::LOGOUT_REASON_ACCOUNT_LOCKED);
    }
}

if(!$authenticated) {
    Arcanum_Logger::log_user('loginfail_wrongpass,uid='.$login_username);
    Arcanum_Session::logoutToLogin(Arcanum_Session::LOGOUT_REASON_WRONG_LOGIN);
    exit;
}


// === Authentication succesful ===
// Everything below this line is for authenticated users

if($adminLogin && $config->institution_domain == 'uoa.gr') {
    // Additional ACL check for UoA only
    include_once('include/uoa/acl.php');
    uoa_setup_restrict($info[0]);
}

// if admin login, then also cache password policies
if($adminLogin) {
    $policies = $arcanumLdap->getPolicies();
}

// Additional bind as user. If this fails, then there is a policy being applied to this
// account and making it fail binds, such as expiredPassword.
// unfortunately we can't get the extended error message in PHP.
    
// discover user's dn:
$userdn = $arcanumLdap->getUserDn($login_username);
        
if($arcanumLdap->connectAsUser($userdn, $password) === false) {
    $msgs = $arcanumLdap->getMsgs();
    $possibly_expired_password = true;
}

// write session data & redirect to main page.
if(!empty($config->recaptcha->pubkey)) {
    $loginProtector->reset_tries();
}
    
Arcanum_Session::init();

// ??? FIXME
$loggedin = true;

$_SESSION['authenticated'] = true;
$_SESSION['cleared_for'] = array('dataentry', 'myaccount', 'passwordreset');
$_SESSION['role'] = $role;

Arcanum_Security::savePassword($password);

if(isset($possibly_expired_password)) {
    $_SESSION['possibly_expired_password'] = true;
}

$startpage = 'home';

$_SESSION['login_username'] = $login_username;
if($adminLogin) {
    $_SESSION['isAdmin'] = $adminLogin;
    $startpage = 'admin';
    if(isset($set_restrict)) {
        $_SESSION['restrict']= $set_restrict;
    }
    if(isset($policies)) {
        $_SESSION['policies']= $policies;
    }
}

if(isset($_POST['startpage'])) {
    if($_POST['startpage'] == 'changepassword') {
        $startpage = 'changepassword';
    }
}

if(isset($_POST['service'])) {
    $_SESSION['service'] = urldecode($_POST['service']);
}

if(isset($info[0]['pwdreset']) && $info[0]['pwdreset'][0] == 'TRUE') {
    $_SESSION['workflow'] = 'pwdreset';
    $startpage = 'changepassword';
}

session_write_close();

header('Location: '.$startpage.'.php');
exit;


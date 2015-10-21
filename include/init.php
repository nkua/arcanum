<?php
/**
 * Initialization Routine
 *
 * @package Arcanum
 * @version $Id: init.php 5954 2012-12-28 10:39:09Z avel $
 */

set_include_path( get_include_path() . PATH_SEPARATOR . './lib/' . PATH_SEPARATOR . './include/' . PATH_SEPARATOR . './lib/phpseclib/');

require_once('lib/Zend/Loader/Autoloader.php');
require_once('include/Arcanum/ExceptionHandler.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Arcanum_');

$defaultStyles = array(
    'lib/bootstrap/css/bootstrap.min.css',
    'style/layout.css',
    'style/forms.css',
    (file_exists('style/custom.css') ? 'style/custom.css' : ''),
);

$defaultAdminStyles = array(
    'lib/bootstrap/css/bootstrap.min.css',
    'lib/bootstrap/css/bootstrap-responsive.min.css',
    'style/admin.css',
);

$defaultJavascripts = array(
    'lib/jquery-1.8.2.min.js',
    'lib/bootstrap/js/bootstrap.min.js',
);

set_exception_handler('Arcanum_ExceptionHandler');


global $config;
$config = (file_exists('config/config.php') ?
    new Zend_Config(require('config/config.php'), true) :
    new Zend_Config(require('include/config.template.php'), true) );

include_once('include/Template.class.php');
include_once('include/misc.php');

date_default_timezone_set($config->timezone);


// ACL definitions
$acl = new Zend_Acl();
$roles = array(
    'anonymous' => new Zend_Acl_Role('anonymous'),
    'user' => new Zend_Acl_Role('user'),
    'admin_password' => new Zend_Acl_Role('admin_password'),
    'admin_policy' => new Zend_Acl_Role('admin_policy'),
    'login_server' => new Zend_Acl_Role('login_server'),
    'installer' => new Zend_Acl_Role('installer'),
);
$acl->addRole($roles['anonymous']);
$acl->addRole($roles['user'], 'anonymous');
$acl->addRole($roles['admin_password'], 'anonymous');
$acl->addRole($roles['admin_policy'], 'admin_password');
$acl->addRole($roles['login_server']);
$acl->addRole($roles['installer']);

$rootDirPages = array('index.php', 'changepassword.php', 'home.php', 'dataentry.php', 'myaccount.php', 'safety.php',
    'ajax_handler.php', 'admin.php', 'admin_show_user.php', 'admin_options.php', 'admin_notifications.php',
    'admin_change_password.php', 'admin_set_policies.php', 'admin_sessions.php', 'setup.php',
    'redirect.php', 'signout.php', 'force_change_password.php', 'reset_password.php', 'api.php',
    'debug.php','serve.php'
);

// ACL Resources and access controls
foreach($rootDirPages as $page) {
    $acl->add(new Zend_Acl_Resource(substr($page, 0, -4)));
}
$acl->allow($roles['anonymous'], array('index', 'signout', 'redirect', 'reset_password', 'api', 'ajax_handler', 'debug','serve'));
$acl->allow($roles['user'], array('changepassword', 'home', 'dataentry', 'myaccount', 'safety', 'ajax_handler'));
$acl->allow($roles['admin_password'], array('admin', 'admin_show_user', 'admin_change_password', 'admin_options', 'admin_notifications', 'admin_sessions', 'ajax_handler', 'changepassword', 'myaccount', 'safety')); 
$acl->allow($roles['admin_policy'], array('admin_set_policies', 'setup'));
$acl->allow($roles['installer'], array('setup')); 
//$acl->allow($roles['login_server'], array('api')); 

// Imporant Variables 
$PHP_SELF = strip_tags($_SERVER['PHP_SELF']);
$baseuri = baseuri();

// Set the session cookie path the same as the app subdirectory. Also, set httpOnly cookie.
session_set_cookie_params(ini_get('session.cookie_lifetime'), $baseuri, '', '', true);

if(!isset($initLocation)) $initLocation = 'undefined';

// Enforce SSL Policy
if($config->ssl_policy > 0) enforce_ssl_policy();

// Login & Session setup procedures
Arcanum_Session::start();

switch($initLocation) {
case 'changepassword':
    // In changepassword, we enable: normal session authentication & token authentication
    // TODO - implement login_protector here
    $loggedin = Arcanum_Session::check(false);
    if(!$loggedin) {
        if(isset($_GET['token']) && !empty($_GET['token'])) {
            $token = $_GET['token'];
        } elseif(isset($_POST['token']) && isset($_POST['sms_token']) && !empty($_POST['token']) ) {
            $token = $_POST['token'];
            // sms token
        }
        if(isset($token)) {
            $token = trim(str_replace('-', '', $token));
            Arcanum_Session::authenticate_token($token);

        } elseif(isset($_GET['casauth']) && $_GET['casauth'] == '1' && !empty($config->cas->host)) {
            Arcanum_Session::authenticate_cas();
            if(!$loggedin) {
                Arcanum_Session::logout(Arcanum_Session::LOGOUT_REASON_CAS_NOT_AUTHENTICATED);
            }
        }
    }
    if(!$loggedin) {
        // still not logged in; sign me out
        Arcanum_Session::logout();
    }

    if(isset($_SESSION['cleared_for'])) {
        $cleared_for = $_SESSION['cleared_for'];
    }

    if(!isset($cleared_for) || !in_array('passwordreset', $cleared_for)) {
        Arcanum_Session::logout(Arcanum_Session::LOGOUT_REASON_WRONG_LOGIN);
    }

    break;

case 'dataentry':
    // In dataentry, we enable: normal session authentication & CAS authentication

    $loggedin = Arcanum_Session::check(false);
    if(!$loggedin && isset($_GET['casauth']) && $_GET['casauth'] == '1' && !empty($config->cas->host)) {
        Arcanum_Session::authenticate_cas();
        if(!$loggedin) {
            Arcanum_Session::logout(Arcanum_Session::LOGOUT_REASON_CAS_NOT_AUTHENTICATED);
        }
    }
    if(!$loggedin) {
        // still not logged in; sign me out
        Arcanum_Session::logout();
    }
    
    if(isset($_SESSION['cleared_for'])) {
        $cleared_for = $_SESSION['cleared_for'];
    }

    if(!in_array('dataentry', $cleared_for)) {
        Arcanum_Session::logout(Arcanum_Session::LOGOUT_REASON_WRONG_LOGIN);
    }

    break;

case 'admin':
case 'admin_show_user':
case 'admin_change_password':
case 'admin_set_policies':
case 'admin_options':
case 'admin_notifications':
case 'admin_sessions':
case 'home':
case 'myaccount':
case 'safety':
    // In most of the pages, we authenticate normally by checking for a valid session
    Arcanum_Session::check();
    break;

case 'setup':
    Arcanum_Session::check_setup();
    break;

case 'api':
    break;

case 'index':
    // TODO - implement login_protector here
    
    $loggedin = Arcanum_Session::check(false);
    if(!$loggedin && isset($_GET['token']) && !empty($_GET['token']) && isset($_GET['expired']) && $_GET['expired'] == 1) {
        Arcanum_Session::authenticate_token();
        if(!$loggedin) {
            // still not logged in; sign me out
            Arcanum_Session::logout();
        }
        if(isset($_SESSION['cleared_for'])) {
            $cleared_for = $_SESSION['cleared_for'];
        }
        $_SESSION['workflow'] = 'pwdexpired';
        $_SESSION['ask_old_password'] = true;

        session_write_close();
        header("Location: changepassword.php");
        exit;
    }

    break;

case 'signout':
    Arcanum_Session::check(false);
    break;

case 'ajax_handler':
case 'redirect':
case 'reset_password':
default:
    break;
}

$isAdmin = false;
if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {
    $isAdmin = true;
    if($initLocation != 'signout') {
        $defaultStyles = $defaultAdminStyles;
    }
}

if(isset($_SESSION['service'])) {
    $service = $_SESSION['service'];
}


$role = Arcanum_Session::getRole();

$restrict = Arcanum_Session::getRestrict();

// If after all the session initialization we are still not allowed, signout immediately

if(!$acl->isAllowed($role, $initLocation)) {
    Arcanum_Session::logout(Arcanum_Session::LOGOUT_REASON_ACCESS_DENIED);
}

setup_locale();

// Important HTTP Headers
header('Pragma: no-cache'); // http 1.0 (rfc1945)
header('Cache-Control: private, no-cache, no-store'); // http 1.1 (rfc2616)

// Set up template instance & basic variables
// TODO - move this to a function / refactor
$t = new Template;
$t->assign('loggedin', $loggedin);
$t->assign('baseuri', $baseuri);
$t->assign('language', $language);
$t->assign('lang', $lang);
$t->assign('institution_name', $config->institution_name);
$t->assign('institution_domain', $config->institution_domain);
$t->assign('title', $config->title);
$t->assign('subtitle', (!empty($config->subtitle) ? $config->subtitle : _("Password Management Service") ) );
$t->assign('initLocation', $initLocation);
$t->assign('isAdmin', $isAdmin);
$t->assign('terms_link', (!empty($config->terms_link) ? $config->terms_link : false) );
$t->assign('privacy_policy_link', (!empty($config->privacy_policy_link) ? $config->privacy_policy_link : false) );

// TODO - move this in redirect.php?
if(isset($_SESSION['possibly_expired_password'])) {
    $t->assign('urgentmsg', 
        _("Your password has expired!") . ' <a href="changepassword.php">'. _("Change Password") . '</a>'
    );
}

$t->assign('defaultStyles', $defaultStyles);
$t->assign('styles', array());
$t->assign('role', $role);
$t->assign('restrict', $restrict);
if(isset($login_username)) {
    $t->assign('login_username', $login_username);
}

// ==================================================================
//
// Function Definitions
//
// ------------------------------------------------------------------


/**
 * Setup Locale
 */
function setup_locale() {
    global $language, $lang, $config;

    $supported_languages = array(
        'el' => 'el_GR',
        'en' => 'en_US',
    );

    if(!isset($language)) {
        $language = $config->locale->default_language;
    }
    $lang = substr($language, 0, 2);

    $locale = setlocale(LC_ALL, "$language.UTF-8", $language, $lang);
    if($locale !== false) {
        if ( !ini_get('safe_mode') && getenv( 'LC_ALL' ) != $locale ) {
            putenv( "LC_ALL=$locale" );
            putenv( "LANG=$locale" );
            putenv( "LANGUAGE=$locale" );
        }
    }
    bindtextdomain('arcanum', 'locale');
    bindtextdomain('password_strength_check', 'locale');
    textdomain('arcanum');
}

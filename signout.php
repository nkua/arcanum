<?php
/**
 * Sign out / Destroy session
 *
 * @package arcanum
 * @version $Id: signout.php 5870 2012-10-25 09:05:49Z avel $
 */
   
$initLocation = 'signout';
require_once('include/init.php');

$success = isset($_GET['success']) ? $_GET['success'] : false;
$forced = isset($_GET['forced']) ? $_GET['forced'] : 0;
$admin = isset($_GET['admin']) ? true : false;
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : false;
$error_message = isset($_GET['error_message']) ? $_GET['error_message'] : '';

if(isset($login_username)) 
    $t->assign('username', $login_username);

if($success && isset($_SESSION['service'])) {
    $service = $_SESSION['service'];
    $t->assign('service', $service);
}

Arcanum_Session::destroy();
if(isset($_SESSION['authenticated_via_cas'])) {
    require_once('CAS/CAS.php');
    phpCAS::client(CAS_VERSION_2_0, $config->cas->host, $config->cas->port, $config->cas->uri, true);
    phpCAS::handleLogoutRequests();
    if(isset($service))
        phpCAS::logout(array('service'=> $service));
    else
        phpCAS::logout();
}

if(isset($service) || $redirect == 'service') {
    // go back to service, via CAS
    header(
        "Location: https://".$config->cas->host . ':' . $config->cas->port .
         ( !empty($config->cas->uri) ? '/'.$config->cas->uri  : '') .
        '/login?service='.rawurlencode($service)
    );

    // alternatively we could redirect back to the service directly; however, if 'renew' was set,
    // this would re-ask the login details by the user. In contrast, by going via the CAS URL
    // where we were already authenticated successfully, the existing login flow is resumed.
    //header("Location: ".$service);

    exit;
} elseif($redirect == 'login') {
    header("Location: index.php");
    exit;
}

$flags = '';
if($forced > 0) {
    $f = array();
    if(isset($_GET['expired'])) {
        $f[] = 'expired=1';
    }
    if(isset($_GET['resetted'])) {
        $f[] = 'resetted=1';
    }

    if(isset($_GET['service'])) {
        $f[] = 'service='.rawurlencode(urldecode($_GET['service']));
    }
    if($f) {
        $flags = '?'.implode('&amp;', $f);
    }


}

/* Presentation */

$t->assign('flags', $flags);
$t->assign('success', $success);
$t->assign('forced', $forced);
$t->assign('admin', $admin);
$t->assign('error_message', $error_message);
$t->assign('javascripts', $defaultJavascripts);

$t->display('signout');

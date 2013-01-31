<?php
/**
 * Change Password Feature.
 *
 * @package arcanum
 * @version $Id: index.php 5895 2012-10-31 13:17:13Z avel $
 */

$initLocation = 'index';

if(!file_exists('config/config.php')) {
    header("Location: setup.php");
    exit;
}

include_once('include/misc.php');
require_once('include/init.php');
require_once('include/LoginProtector.php');

$loginProtector = new LoginProtector();
// check our xcache stats to see if we need to require captcha
if($loginProtector->get_tries() >= 5) {
    include_once('Zend/Captcha/ReCaptcha.php');
    $recaptcha = new Zend_Service_ReCaptcha($config->recaptcha->pubkey, $config->recaptcha->privkey);
    $recaptcha->setParams(array('ssl' => true, 'theme' => 'clean'));
    $t->assign('captcha_html', $recaptcha->getHTML());
}


// Check if we were redirected from CAS because of an expired password

$renew = false;
if(isset($_GET['renew']) && $_GET['renew'] == 'true') {
    $renew = true;
}
$expired = false;
if(isset($_GET['expired']) && $_GET['expired'] == '1') {
    $expired = true;
}
$resetted = false;
if(isset($_GET['resetted']) && $_GET['resetted'] == '1') {
    $resetted = true;
}

if(isset($_GET['service'])) {
    $service = urldecode($_GET['service']);
}

if(isset($_GET['displaymsg']) && isset($_GET['forced']) ) {
    $forced = $_GET['forced'];
    
    if(isset($_GET['msg'])) {
        $msg = urldecode($_GET['msg']);
    } else {
        $msg = Arcanum_Session::getLogoutReasonMessage($forced);
    }
    $t->assign('message_above_login_box', $msg);
    
}

// === Presentation ===

$t->assign('usernameValue', '');
$t->assign('renew', $renew);
$t->assign('expired', $expired);
$t->assign('resetted', $resetted);

if(isset($service)) {
    $t->assign('service', $service);
}

if(isset($service)) {
    $t->assign('service', $service);
}

$t->assign('loginMotd',!empty($config->motd) ? $config->motd : '');
$t->assign('intro', !empty($config->intro) ? $config->intro : '');

$t->assign('javascripts', $defaultJavascripts);

$t->display('login');


<?php
/**
 * @package arcanum
 * @version $Id: reset_password.php 5961 2013-01-04 13:10:38Z avel $
 */

$initLocation = 'reset_password';
require_once('include/init.php');

include_once('lib/password_generator/password_generator.inc.php');
include_once('lib/password_strength_check/functions.inc.php');
include_once('lib/password_strength_check/password_strength_check_collection.class.php');
include_once('lib/password_strength_check/password_strength_check.class.php');

include_once('Zend/Captcha/ReCaptcha.php');

$t->assign('javascripts', $defaultJavascripts);

if(isset($_POST['reset_password_abort'])) {
    header("Location: signout.php");
    exit;
}

$all_methods = array();
foreach($config->ldap->secondary_accounts->toArray() as $m => $ldapattr) {
    if(!empty($ldapattr)) {
        $all_methods[] = $m;
    }
}
unset($m);
unset($ldapattr);

if(empty($all_methods)) exit;

$t->assign('all_methods', $all_methods);


if(isset($_POST['reset_password_do'])) {
    if(empty($_POST['method'])) abort();
    $method = $_POST['method'];

    if(isset($_POST['login_username']) && !empty($_POST['login_username'])) {
        $login_username = $_POST['login_username'];
        $_SESSION['reset_password'] = true;
        $_SESSION['login_username'] = $login_username;
        $t->assign('login_username', $login_username);
    }
    
    foreach($config->ldap->secondary_accounts->toArray() as $m => $ldapattr) {
        if(!empty($ldapattr) && isset($_POST['reset_password_confirm_'.$m])) {
            $_SESSION['method'] = $method = $m;
            break;
        }
    }
    switch($method) {
    case 'sms':
        $required_info = array('surname', 'sms');
        break;
    case 'email':
        $required_info = array('surname', 'email');
        break;
    }
    $_SESSION['required_info'] = $required_info;
    $t->assign('required_info', $required_info);
        
    // a long and tedious verification process

    $msgs = array();
    $encounterederror = false;

    if(!isset($_SESSION['login_username'])) {
        $encounterederror = true;
        $msgs[] = array('class' => 'warning', 'msg' => _("Some of the information you entered was invalid (e.g. wrong username, wrong mobile number, not registered e-mail address)."));
    }
    if(!isset($_SESSION['required_info'])) {
        $encounterederror = true;
        $msgs[] = array('class' => 'error', 'msg' => _("Required information was not filled in"));
    }

    if(!empty($config->recaptcha->pubkey)) {
        $recaptcha = new Zend_Service_ReCaptcha($config->recaptcha->pubkey, $config->recaptcha->privkey);
        $recaptcha->setParams(array('ssl' => true, 'theme' => 'clean'));
      
        if(empty($_POST['recaptcha_challenge_field']) || empty($_POST['recaptcha_response_field'])) {
            $msgs[] = array('class' => 'warning', 'msg' => _("Please type the two words in the box."));
            $encounterederror = true;

        } else {
            $captcha_result = $recaptcha->verify(
                $_POST['recaptcha_challenge_field'],
                $_POST['recaptcha_response_field']
            );
            if (!$captcha_result->isValid() && !$config->devel->allow_all_captcha) {
                $msgs[] = array('class' => 'warning', 'msg' => _("Captcha verification failed; please retry typing the two words in the box."));
                $encounterederror = true;
            }
        }
    }

    $supplied_info = array();
    foreach($required_info as $required) {
        if(isset($_POST[$method.'_'.$required]) && !empty($_POST[$method.'_'.$required])) {
            $supplied_info[$required] = $_POST[$method.'_'.$required];
        } else {
            $msgs[] = array('class' => 'error', 'msg' => _("You did not fill in a required field. Please try again."));
            abort($msgs);
        }
    }

    if($encounterederror === true) {
        abort($msgs);
    }

    $arcanumLdap = new Arcanum_Ldap();
    $ldap = $arcanumLdap->connect();
    if(!$ldap) {
        $msgs[] = array('class' => 'error', 'msg' => _("Could not connect to the account database. Please try again later."));
        abort($msgs);
    }

    $login_username = $_SESSION['login_username'];

    $ldapattrs = array();
    foreach($config->ldap->secondary_accounts->toArray() as $m => $ldapattr) {
        if(!empty($ldapattr)) {
            $ldapattrs[] = strtolower($ldapattr);
        }
    }

    foreach($required_info as $required) {
        // a bit ugly, FIXME
        if($required == 'surname') {
            $ldapattrs[] = 'sn';
        } else {
            $ldapattrs[] = strtolower($config->ldap->secondary_accounts->$required);
        }
    }
    $ldapattrs = array_values($ldapattrs);

    $sr = ldap_search($ldap, $config->ldap->basedn, sprintf($config->ldap->filter->user, ldapspecialchars($login_username)),
        array_merge($ldapattrs, array('uid', '+')));

	$entries = ldap_get_entries($ldap, $sr);
    if($entries['count'] != 1) {
        $msgs[] = array('class' => 'warning', 'msg' => _("Some of the information you entered was invalid (e.g. wrong username, wrong mobile number, not registered e-mail address)."));
        abort($msgs);
    }

    $uid = $entries[0]['uid'][0];

    foreach($required_info as $required) {
        // checking for required info "$required", mapped to ldapattr $attr
        if($required == 'surname') {
            $attr = 'sn';
        } else {
            $attr = strtolower($config->ldap->secondary_accounts->$required);
        }
        //print "<br> verification - ". $supplied_info[$required] . " vs " . $entries[0][$attr][0];

        if(!isset($entries[0][$attr][0]) || $entries[0][$attr]['count'] == 0) {
            $msgs[] = array('class' => 'error', 'msg' => _("There is insufficient information to continue the reset password process via this method. (E.g. you had't filled in your e-mail address or your mobile phone number in the past)."));
            abort($msgs);
        }


        $res = false;
        if($attr == 'cn' || $attr == 'sn' || $attr == 'givenname') {
            $res = canonicalize_greek_text($supplied_info[$required]) == canonicalize_greek_text($entries[0][$attr][0]);

        } elseif($attr == 'mobile') {
            // FIXME should have the generic, variable LDAP attribute for mobile
            $mobile_form_object = Arcanum_Form::Factory('sms');
            $mobile_form_object->setInputValue($supplied_info[$required]);

            $res = ($mobile_form_object->getNormalizedValue()  == $entries[0][$attr][0]);

        } else {
            $res = (mb_strtolower($supplied_info[$required], 'UTF-8') == mb_strtolower($entries[0][$attr][0], 'UTF-8'));
        }
        if($res) {
            // all ok
        } else {
            $msgs[] = array('class' => 'warning', 'msg' => _("Some of the information you entered was invalid (e.g. wrong username, wrong mobile number, not registered e-mail address)."));
            abort($msgs);
        }
    }

    // all tests and verifications passed. generate token to reset the password:
    // TODO: check if there is already a token with this mobile number!!! important!
    /*
    if($method == 'sms') {
        // simpler token for sms so that user can enter it easily.
        $token = $tokenstore->generate_token_number();
        $tokenstore->set_sms_token(array($token, $uid, 1), '6938784583');
    } else
     */

    if($method == 'email') {
        Arcanum_Session::destroy();

        $tokenstore = new Arcanum_Token_Email;
        $token = $tokenstore->generate_token();
        $tokenstore->set_token($token, $uid);
        
        arcanumSetupEmail();
        include_once('Zend/View.php');
        $body = new Zend_View();
        $body->setScriptPath('./templates/emails');
        $body->website_home = $config->website_home;
        $body->token = $token;

        $zmail = new Zend_Mail('UTF-8');
        $zmail->setSubject( sprintf( _("Password reset for your account at %s"), $config->institution_name));
        $zmail->setBodyText($body->render('mail_reset_password.tpl.php'));
        $zmail->addTo($supplied_info['email']);

        if(!empty($config->devel->email_cc)) {
            $zmail->addCc($config->devel->email_cc);
        }
        $zmail->send();
        
        $msgs[] = array('class' => 'success', 'msg' =>
           _("Your details have been confirmed. You will receive an e-mail with further instructions on how to enter a new password.")
           );

        $t->assign('success_title', ("Confirm your account information to reset your password") );
        $t->assign('msgs', $msgs);
        
        $t->display('html_header');
        $t->display('page_header');

        $t->display('success_messages');

        $t->display('page_footer');
        $t->display('html_footer');
        exit;


    } elseif($method == 'sms') {
        // save username in env store / initiated_reset_pw array
        $envStore = new Arcanum_EnvironmentStore();

        $tmp = $envStore->get('initiated_reset_pw');
        if($tmp === false) {
            $initiated_reset_pw = array();
        } else {
            $initiated_reset_pw = $tmp;
        }
        $initiated_reset_pw[$uid] = true;
        $envStore->set('initiated_reset_pw', $initiated_reset_pw);
        unset($tmp);

        $t->assign('javascripts', array_merge($defaultJavascripts, array('javascripts/sms_token_check.js')));
        $t->assign('sms_to', $config->sms_operator_number);
        $t->assign('sms_body', strtoupper($config->smsgw->prefix));
        $flow = '';

        $t->display('html_header');
        $t->display('page_header');

        $t->display('reset_password_wait_for_sms');

        $t->display('page_footer');
        $t->display('html_footer');
        exit;



    }
} else {
    // 1st step, displaying form
    $t->assign('all_methods', $all_methods);
    
    if(sizeof($all_methods) > 1 ) {
        $t->assign('javascripts', array_merge($defaultJavascripts, array('javascripts/reset_password_start.js')));
    }

    if(!empty($config->recaptcha->pubkey)) {
        $recaptcha = new Zend_Service_ReCaptcha($config->recaptcha->pubkey, $config->recaptcha->privkey);
        $recaptcha->setParams(array('ssl' => true, 'theme' => 'clean'));
        $t->assign('captcha_html', $recaptcha->getHTML());
    }
    
}



// ====== Presentation ======

$t->display('html_header');
$t->display('page_header');

$t->display('reset_password_start');

$t->display('page_footer');
$t->display('html_footer');

// ====== functions ======

function abort($msgs = false) {
    //    destroy_session();
    global $t, $defaultJavascripts;
    
    $t->assign('javascripts', $defaultJavascripts);
    $t->display('html_header');
    $t->display('page_header');
    
    if($msgs) $t->assign('msgs', $msgs);

    $t->display('reset_password_retry');
    $t->display('page_footer');
    $t->display('html_footer');

    exit;
}

function canonicalize_greek_text($str) {
    return str_replace(
        array('ά','έ','ή','ί','ό','ύ','ώ', 'ϊ', 'ΐ', 'ϋ', 'ΰ', 'ς'),
        array('α','ε','η','ι','ο','υ','ω', 'ι', 'ι', 'υ', 'υ', 'σ'),
        mb_strtolower($str, 'UTF-8') );

}

function format_number_token($token) {
    return substr($token, 0, 3) . ' ' .
        substr($token, 3, 3) . ' ' .
        substr($token, 6, 3) . ' ' .
        substr($token, 9, 3);
}


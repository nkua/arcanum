<?php
/**
 * Change Password Main form
 *
 * @package arcanum
 * @version $Id: changepassword.php 5892 2012-10-31 11:05:29Z avel $
 */
   
$initLocation = 'changepassword';
require_once('include/init.php');

include_once('password_strength_check/functions.inc.php');
include_once('password_strength_check/password_strength_check_collection.class.php');
include_once('password_strength_check/password_strength_check.class.php');

global $success;
$success = false;
$msgs = array();

if(isset($_SESSION['service'])) {
    $service = $_SESSION['service'];
}

$reset_forgotten_password_enabled = (isset($_SESSION['reset_forgotten_password_enabled']) ? true : false);

$workflow = '';
if(isset($_SESSION['workflow'])) {
    $workflow = $_SESSION['workflow'];
}

$ask_old_password = false;
if(isset($_SESSION['ask_old_password'])) {
    $ask_old_password = true;
}

// default value
$change_password_as = Arcanum_LdapPassword::AS_USER;

$possibly_expired_password = false;
if(isset($_SESSION['possibly_expired_password'])) {
    $possibly_expired_password = true;
}

// ------------- Change Password -------------
if (isset($_POST['changepass_do'])) {
    global $username;
    $username = $_SESSION['login_username'];

    $arcanumLdap = new Arcanum_LdapPassword();

    $ldap = $arcanumLdap->connect();

    $arcanumLdap->setParameters(array(
        'username' => $username,
        'proxy' => false,
        'force_change' => true,
    ));

    if($ask_old_password === true) {

        $oldpass = $_POST['cp_oldpass'];
        $arcanumLdap->setParameters(array(
            'oldpass' => $oldpass,
            'proxy' => false,
            'force_change' => true,
        ));

    } else {
        $oldpass = Arcanum_Security::readPassword();
        if($oldpass === false) {
            $arcanumLdap->setParameters(array(
                'proxy' => true,
                'force_change' => false
            ));
        } else {
            $arcanumLdap->setParameters(array(
                'oldpass' => $oldpass,
                'proxy' => false,
                'force_change' => true,
            ));
        }
    }
    
    $arcanumLdap->setParameters(array(
        'newpass' => $_POST['cp_newpass'],
        'verify'  => $_POST['cp_verify'],
    ));
    
    $arcanumLdap->validateParameters();
    $msgs = $arcanumLdap->getMsgs();
    $error_encountered = false;

    if(!empty($msgs)) {
        $error_encountered = true;
    } else {
        // if expired, then connectAsUser() would fail
        // so we set pwdReset = TRUE
        if($possibly_expired_password) {
            if($arcanumLdap->allowUserPasswordReset() === false) {
                $error_encountered = true;
                $msgs = array_merge($msgs, $arcanumLdap->getMsgs());
            }
        } elseif($reset_forgotten_password_enabled) {

            // password reset - but we don't know the old password!
            
            // at the moment, we simply change the password by binding as admin
            // the policy of old password checks is not applied this way.
            
            $change_password_as = Arcanum_LdapPassword::AS_ADMIN;

            /* 
            // alternative solution:  we'll put a temporary password in LDAP which we'll use to bind.
            // this has the problem that the pwdMinAge policy will be enforced

            include_once('lib/password_generator/password_generator.inc.php');
            $temppass = password_generate_random();
            $arcanumLdap->setParameters(array(
                'newpass' => $temppass,
            ));
            $arcanumLdap->changeUserPassword(AS_ADMIN);

            $oldpass = $temppass;
            
            $arcanumLdap->setParameters(array(
                'oldpass' => $temppass,
                'newpass' => $_POST['cp_newpass'],
                'verify'  => $_POST['cp_verify'],
            ));
            
            if($arcanumLdap->allowUserPasswordReset() === false) {
                $error_encountered = true;
                $msgs = array_merge($msgs, $arcanumLdap->getMsgs());
            }
             */
        }

        // and then we connect as user.

        // discover user's dn:
        $userdn = $arcanumLdap->getUserDn($username);
        
        // and mobile, if it exists and user is allowed to receive SMS:
        $mobile_number = false;
        if(!empty($config->ldap->filter->user_receivesms)) {
            $sr = ldap_search($ldap, $config->ldap->basedn,
                sprintf($config->ldap->filter->user, Arcanum_Ldap::specialchars($username)),
                array('mobile'));
            $info = ldap_get_entries($ldap, $sr);
            if($info['count'] == 1 && isset($info[0]['mobile']) && isset($info[0]['mobile'][0])) {
                $mobile_number = $info[0]['mobile'][0];
            }
            unset($info);
        }

        if($change_password_as === Arcanum_LdapPassword::AS_USER && $arcanumLdap->connectAsUser($userdn, $oldpass) === false) {
            $error_encountered = true;
            $msgs[] = array('class' => 'error', 'msg' => _("Your old password is not correct."));
        }

        if(!$error_encountered) {
           if($arcanumLdap->changeUserPassword($change_password_as) === true) {

                if($arcanumLdap->changeUserAdditionalPasswordAttributes() === true) {
                    if($mobile_number) {
                        $sms = Arcanum_SMS_Sender::Factory($mobile_number,$config->smsgw);
                        $sms->send(_("This is an automated notification to inform you that your password was just changed. - ".$config->institution_name));
                    }

                    if(isset($service)) {
                        $redirect = 'service';
                        $t->assign('service', $service);
                        $timeout = "14";
                    } else {
                        $redirect = 'login';
                        $timeout = "10";
                    }

                    $redirect_link = 'signout.php?success=1&amp;redirect='.$redirect;

                    $t->assign('redirect', $redirect);
                    $t->assign('timeout', $timeout);
                    $t->assign('redirect_link', $redirect_link);

                    $t->assign('xtra_head', '<meta http-equiv="refresh" content="'.$timeout.';URL=\''.$redirect_link.'\'" />');
                    $t->assign('javascripts', $defaultJavascripts);

                    $t->display('html_header');
                    $t->display('page_header');

                    $t->display('change_password_redirecting');
                    $t->display('page_footer');
                    $t->display('html_footer');

                    // header('Location: signout.php?success=1');
                    exit;
                }

            } else {
                $error_encountered = true;
                $msgs = $arcanumLdap->getMsgs();
            }
        }
    }
}

// Displaying form; grab policy for strength check to show.
$check = new passwordStrengthCheck($config->password_strength_policy->toArray());
$allStrengthMessages = $check->allTestMessages(true);


/* === Presentation Logic === */

$t->assign('workflow', $workflow);
$t->assign('ask_old_password', $ask_old_password);
$t->assign('msgs', $msgs);
$t->assign('allStrengthMessages', $allStrengthMessages);
$t->assign('login_username', $_SESSION['login_username']);
$t->assign('javascripts', array_merge(
    $defaultJavascripts,
    array(
        'lib/jquery.delayedObserver.js',
        'lib/underscore-min.js',
        'javascripts/change_password.js',
    )
));

$t->display('html_header');

$t->display('page_header');
if(empty($cleared_for) || sizeof($cleared_for) > 1) {
    $t->display('navigation_user');
}

if(isset($_GET['token']))
    $t->display('logged_in_as');


$t->display('change_password');
$t->display('page_footer');

$t->display('html_footer');


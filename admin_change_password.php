<?php
/**
 * Change password of other users (admin form).
 *
 * @package arcanum
 * @version $Id: admin_change_password.php 5859 2012-10-19 13:07:55Z avel $
 */

$initLocation = 'admin_change_password';
require_once('include/init.php');

$msgs = array();

// Set up ldap handle and check EVERY TIME if user is authorized to be in this page via LDAP.
// This is to avoid potential session fixation / session hijacking or even XSS vulnerabilities

$arcanumLdap = new Arcanum_LdapPassword();
$ldap = $arcanumLdap->connect();

$admins_filter = '(|'.sprintf($config->ldap->filter->admin_password, ldapspecialchars($login_username)).
   sprintf($config->ldap->filter->admin_policy, ldapspecialchars($login_username)).')';

$sr = ldap_search($ldap, $config->ldap->basedn, $admins_filter, array('uid', 'userpassword'));
if(!$sr) die('Error searching!');
$info = ldap_get_entries($ldap, $sr);
Arcanum_Ldap::sanitize_entry_array($info);

if($info['count']!=1) {
    Arcanum_Session::logout(Arcanum_Session::LOGOUT_REASON_WRONG_LOGIN);
}
unset($info);

if(isset($_POST['changepass'])) {
	/* Actual change of password */

    // include library: password_strength_check
    include_once('password_strength_check/functions.inc.php');
    include_once('password_strength_check/password_strength_check_collection.class.php');
    include_once('password_strength_check/password_strength_check.class.php');

	$uid = $_POST['uid'];
	$dn = urldecode($_POST['dn']);
	if(isset($_POST['newpass'])) {
		$newpass = $_POST['newpass'][0];
		$verify = $_POST['newpass'][1];
	}

    // check _again_ if we are allowed to modify the entry
    if($restrict) {
        $filter = '(&'.sprintf($config->ldap->filter->user, ldapspecialchars($uid)).$restrict['apply'].')';
        $sr = ldap_search($ldap, $config->ldap->basedn, $filter, array_merge($config->admin->show_attrs->toArray(), array('objectclass')) );
        if(ldap_count_entries($ldap, $sr) != 1) {
            Arcanum_Session::logout(Arcanum_Session::LOGOUT_REASON_ACCESS_DENIED);
        }
    }

    // continue with password change
    $arcanumLdap->setParameters(array(
        'username' => $uid,
        'proxy' => true,
        'force_change' => false,
        'newpass' => $newpass,
        'verify' => $verify,
    ));
    $arcanumLdap->validateParameters();
    $arcanumLdap->check();
    $msgs = $arcanumLdap->getMsgs();
    $error_encountered = false;

    if(!empty($msgs)) {
        $error_encountered = true;
    }

	if($error_encountered === false) {
        if($arcanumLdap->changeUserPassword(Arcanum_LdapPassword::AS_ADMIN) !== true) {
            $msgs = $arcanumLdap->getMsgs();
            $error_encountered = true;
        } else {
            $msgs[] = array('class' => 'success', 'msg' => sprintf( _("Changed password for user %s successfully"), $uid));
        }
    }

	if($error_encountered === false) {
        if($arcanumLdap->changeUserAdditionalPasswordAttributes() !== true) {
            $msgs = array_merge($msgs, $arcanumLdap->getMsgs());
            $error_encountered = true;
        }
    }

	if($error_encountered === false) {
        if(isset($_POST['expire_pass_immediately']) && $_POST['expire_pass_immediately'] == 1) {
            $ldapmod = array('pwdreset' => 'TRUE');
            if( @ldap_modify($ldap, $dn, $ldapmod) !== true) {
                $msgs[] = array('class' => 'error', 'msg' => sprintf( _("Could not force user to change pasword. (User's dn: %s)"), $dn));
            }
        }
    }
		
} elseif(!empty($_REQUEST['uid'])) {
	/* Username confirmation screen */
	$uid = $_REQUEST['uid'];

    $filter = sprintf($config->ldap->filter->user, ldapspecialchars($uid));
    if($restrict) {
        $filter = '(&'.$filter.$restrict['apply'].')';
    }
    $sr = ldap_search($ldap, $config->ldap->basedn, $filter, array_merge($config->admin->show_attrs->toArray(), array('objectclass')) );
	$entry = ldap_get_entries($ldap, $sr);
    Arcanum_Ldap::sanitize_entry_array($entry);
    
    $error_encountered = false;

    if($entry['count'] == 0) {
        $error_encountered = true;
		$msgs[] = array('class' => 'warning', 'msg' => _("Username not found."));
	} elseif($entry['count'] > 1) {
        $error_encountered = true;
        $msgs[] = array('class' => 'danger', 'msg' =>  _("Multiple entries with the same username found"));
    }

} else {
    // no user selected, redirect to search form
    header("Location: admin_show_user.php");
    exit;
}


$exported_display_vars = array();
foreach($config->admin->summary_attrs->toArray() as $attr) {
    $exported_display_vars[$attr] = $arcanumLdap->attributes[$attr]['desc'];
}
$inlinejavascript = 'var summaryAttrs = '.json_encode($exported_display_vars) .';';


// ----------- Presentation Logic ---------------

// FIXME - Perhaps I should export this more elegantly.
$t->assign('arcanumLdap', $arcanumLdap);

$t->assign('javascripts',
    array_merge(
        $defaultJavascripts, array(
            'javascripts/admin_change_password.js',
            'javascripts/admin_choose_user.js'
    )
));
$t->assign('inlinejavascript', $inlinejavascript);

$t->assign('msgs', $msgs);

$t->display('html_header');
$t->display('page_header_admin');


if(isset($_POST['changepass'])) {
	// Change password and success (or, error) message
    $t->assign('error_encountered',   $error_encountered);
    
    $t->display('admin_change_password_result');

} elseif(!empty($uid)) {
	// Confirm user and change password form
	if($error_encountered) {
        $t->display('messages');

	} else {
		$i = 0;

        $show_attrs = $config->admin->show_attrs->toArray();
        $t->assign('userinfo', $entry[$i]);
        $t->assign('show_attrs', $show_attrs);
        $t->assign('perform_strength_checks', $config->admin->perform_strength_checks);
        
        $t->display('messages');
        $t->display('admin_change_password');
    }
        
	
}

$t->display('page_footer_admin');
$t->display('html_footer');


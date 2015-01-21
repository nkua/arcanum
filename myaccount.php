<?php
/**
 * Change Password Main form
 *
 * @package arcanum
 * @version $Id: myaccount.php 5850 2012-10-11 06:48:27Z avel $
 */
   
$initLocation = 'myaccount';
require_once('include/init.php');

$msgs = array();

$arcanumLdap = new Arcanum_Ldap();
$ldap = $arcanumLdap->connect();
$opt_attribute = $config->ldap->optinattibute;

$secondary_accounts_ldapattrs = array();
foreach($config->ldap->secondary_accounts->toArray() as $m => $ldapattr) {
    if(!empty($ldapattr)) {
        $secondary_accounts_ldapattrs[] = $ldapattr;
    }
}
$sr = ldap_search($ldap, $config->ldap->basedn, sprintf($config->ldap->filter->user, ldapspecialchars($login_username)),
        array_merge($secondary_accounts_ldapattrs, array('objectclass', $opt_attribute, '+')));

$entries = ldap_get_entries($ldap, $sr);
$userdn = $entries[0]['dn'];

$entry = array();

if(!in_array('extendedAuthentication', $entries[0]['objectclass'])) {
    $entry['objectclass'][] = 'extendedAuthentication';
    if(ldap_mod_add($ldap, $userdn, $entry) === false) {
        $msgs[] = array('class' => 'error', 'msg' => sprintf( _("Attention: your record in the directory server cannot be modified. Please contact your administrator. (LDAP Error: %s)"),
            ldap_error($ldap)) );
    }
}

if(!in_array('pwdManagement', $entries[0]['objectclass'])) {
    $entry['objectclass'][] = 'pwdManagement';
    if(ldap_mod_add($ldap, $userdn, $entry) === false) {
        $msgs[] = array('class' => 'error', 'msg' => sprintf( _("Attention: your record in the directory server cannot be modified. Please contact your administrator. (LDAP Error: %s)"),
            ldap_error($ldap)) );
    }
}



$t->assign('secondary_accounts', $config->ldap->secondary_accounts->toArray());

$opted_out = false;
if(isset($entries[0][$opt_attribute]) && $entries[0][$opt_attribute][0] == TRUE) {
    $opted_out = true;
}

// find out when password expires

// first , policy. (TODO: refactor)
$sr = ldap_search($ldap, $config->ldap->basedn, '(objectclass=pwdpolicy)',
    array_merge(array('cn'), array_keys($arcanumLdap->policyAttributes)) );
$aentries = ldap_get_entries($ldap, $sr);
if($aentries['count'] != 0) {
    $policy = $aentries[0];
    $pwdmaxage = $aentries[0]['pwdmaxage'][0];
}
unset($aentries);

if(isset($pwdmaxage)) {
    $currenttime = time();
    $changedtime = Arcanum_LdapPassword::getChangedTime($entries[0]);
    $age = $currenttime - $changedtime;

    if($age < $pwdmaxage) {
        $expires_in = 0;
    } else {
        $expires_in = $pwdmaxage - $age;
    }
    $t->assign('age', $age);
    $t->assign('expires_in', $expires_in);
}



if(isset($_SESSION['possibly_expired_password'])) {
    $possibly_expired_password = true;
    $t->assign('possibly_expired_password', true);
}

// gather all secondary accounts values
$secondary_accounts_values = array();
foreach($config->ldap->secondary_accounts->toArray() as $m => $ldapattr) {
    if(!empty($ldapattr)) {
        $formobject = Arcanum_Form::Factory($m);
        if(!empty($entries[0][strtolower($ldapattr)])) {
            $formobject->setValueFromStore($entries[0][strtolower($ldapattr)][0]);
            $secondary_accounts_values[$m] = $formobject->getDisplayValue();
        }
        unset($formobject);
    }
}
$sr = ldap_search($ldap, $config->ldap->basedn, sprintf($config->ldap->filter->user, ldapspecialchars($login_username)),
        array_merge($secondary_accounts_ldapattrs, array('objectclass', $opt_attribute, '+')));


$t->assign('secondary_accounts_values', $secondary_accounts_values);
$t->assign('entry', $entries[0]);
if(isset($entries[0]['pwdaccountlockedtime'])) {
    $t->assign('lockedtime', $entries[0]['pwdaccountlockedtime']);
}
$t->assign('opted_out', $opted_out);

// === Presentation Logic === 

$t->assign('msgs', $msgs);
$t->assign('javascripts', $defaultJavascripts);

$t->display('html_header');

if($isAdmin) {
    $t->display('page_header_admin');
} else {
    $t->display('page_header');
    if(empty($cleared_for) || sizeof($cleared_for) > 1) {
        $t->display('navigation_user');
    }
}

$t->display('myaccount');

if($isAdmin) {
    $t->display('page_footer_admin');
} else {
    $t->display('page_footer');
}
$t->display('html_footer');


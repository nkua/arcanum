<?php
/**
 * Data entry form for secondary accounts
 *
 * @package arcanum
 * @version $Id: dataentry.php 5849 2012-10-11 06:27:20Z avel $
 */
   
$initLocation = 'dataentry';
require_once('include/init.php');

$msgs = array();

if(isset($service) && isset($_POST['done'])) {
    header("Location: signout.php");
    exit;
}

$ask_old_password = ( (isset($_SESSION['ask_old_password']) && $_SESSION['ask_old_password'] === true) ? true : false);
$submit_optout = ((isset($_GET['enable_optout']) && $_GET['enable_optout'] == 1) ? true : false);

$arcanumLdap = new Arcanum_Ldap();
$ldap = $arcanumLdap->connect();

$secondary_accounts_ldapattrs = array();
$opt_attribute = $config->ldap->optinattibute;

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

// Handle submit of new values.

// First of all check for password.
// There is a modification request, so here is where we will remove the 
// check for and remove the ask_old_password flag if it exists.

$modified = false;
$modallowed = false;

if($ask_old_password === true) {
    $modrequest = (isset($_POST['submit_values']) ? true : false);

    if($modrequest === true) {
        if(!isset($_POST['pass']) || empty($_POST['pass'])) {
            $msgs[] = array('class' => 'error', 'msg' => _("Please enter your password to register any of these attributes."));
        } else {
            $pass = $_POST['pass'];
            $aLdap = new Arcanum_LdapPassword();
            if($aLdap->connectAsUser($userdn, $pass) === false) {
                $msgs[] = array('class' => 'error', 'msg' => _("Your password is not correct. Please try again. If the password has expired, you will have to change it first."));
            } else {
                $modallowed = true;

                // and here we reset the flag! :)
                $ask_old_password = false;
                $_SESSION['ask_old_password'] = false;
            }
        }
    }
} else {
    $modallowed = true;
}

if($modallowed === true) {

$newvalues = array();
//TODO fix ldapattr
    foreach($config->ldap->secondary_accounts->toArray() as $method => $ldapattr) {
        if(empty($ldapattr)) continue;

        if(isset($_POST['submit_values']) && isset($_POST[$method]) ) {
            $formobject = Arcanum_Form::Factory($method);
            $val = $_POST[$method];
            $formobject->setInputValue($val);

            if(!empty($val)) {
                // add / modify
                if(($res = $formobject->validate()) === true) {
                    // input value validated

                    $ldapvalue = $formobject->getNormalizedValue();

                    //Quick and dirty changes not working properly TODO
                    $ldapvalue = $ldapattr['prefix'].$ldapvalue;
                    $newvalues[$ldapattr['attribute']][] =  $ldapvalue;
                    
                    //Do not modify here but wait to gather all values
                    //ldap_modify($ldap, $userdn, array( $ldapattr['attribute'] => $ldapvalue) );
                    //$modified = true;
                } else {
                    $msgs[] = array('class' => 'error', 'msg' => $res);
                }
            } else {
             
                //No do not run here
                //
                // delete
               // ldap_modify($ldap, $userdn, array( $ldapattr['attribute'] => array() ));
               // $modified = true;
            }
            unset($val);
        }
    }
             
    if(isset($_POST['submit_values']) && isset($_POST[$method])) {
        if( empty($newvalues) ){
           foreach($config->ldap->secondary_accounts->toArray() as $method => $ldapattr) {
                ldap_modify($ldap, $userdn, array_merge(array($opt_attribute => 'FALSE') ,array( $ldapattr['attribute'] => array() )));  
                $modified = true;
            }
        }else {
            ldap_modify($ldap, $userdn, array_merge( array($opt_attribute => 'TRUE'),$newvalues));
            $modified = true;
        }

    }
}



if($submit_optout) {
    ldap_modify($ldap, $userdn, array( $opt_attribute => 'TRUE'));
    $modified = true;

    // if we have a service, redirect straight to there
    if(isset($service)) {


    Arcanum_Session::destroy();

    header(
        "Location: https://".$config->cas->host . ':' . $config->cas->port .
         ( !empty($config->cas->uri) ? '/'.$config->cas->uri  : '') .
        '/login?service='.rawurlencode($service)
    );
        exit;
    }
}

if($modified === true) {
    // re-get entry
    $sr = ldap_search($ldap, $config->ldap->basedn, sprintf($config->ldap->filter->user, ldapspecialchars($login_username)),
            array_merge($secondary_accounts_ldapattrs, array($opt_attribute, '+')));
    $entries = ldap_get_entries($ldap, $sr);
}

$opted_out = false;
if(isset($entries[0][$opt_attribute]) && $entries[0][$opt_attribute][0] === TRUE) {
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

    if($age > $pwdmaxage) {
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


$secondary_accounts_values = array();
// gather all secondary accounts values
if (!empty($config->ldap->secondary_accounts )) {
    $secondary_accounts_values = $arcanumLdap->getSecondaryAccounts($login_username);
}
//$secondary_accounts_values = array();
/*
foreach($config->ldap->secondary_accounts->toArray() as $m => $ldapattr) {
    $secondary_accounts_values[$m] = '';
    if(!empty($ldapattr)) {
        $formobject = Arcanum_Form::Factory($m);
        if(!empty($entries[0][strtolower($ldapattr)])) {
            $formobject->setValueFromStore($entries[0][strtolower($ldapattr)][0]);
            $secondary_accounts_values[$m] = $formobject->getDisplayValue();
        }
        unset($formobject);
    }
}
*/

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
$t->assign('ask_old_password', $ask_old_password);
$t->assign('formtarget', 'dataentry.php');
$t->assign('modified', $modified);
if(isset($service)) {
    $t->assign('service', $service);
}

$t->display('html_header');
$t->display('page_header');
if($isAdmin) {
    $t->display('navigation_admin');
} else {
    if(empty($cleared_for) || sizeof($cleared_for) > 1) {
        $t->display('navigation_user');
    }
}

$t->display('dataentry');

$t->display('page_footer');
$t->display('html_footer');



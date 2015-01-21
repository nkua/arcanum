<?php
/**
 * Change password of other users (admin form).
 *
 * @package arcanum
 * @version $Id: admin_show_user.php 5961 2013-01-04 13:10:38Z avel $
 */

$initLocation = 'admin_show_user';
require_once 'include/init.php';


$show_pwdreset_form = true;
if($config->ldap->servertype == 'sunds') {
    $show_pwdreset_form = false;
}

$policies = $_SESSION['policies'];

$arcanumLdap = new Arcanum_Ldap();
$ldap = $arcanumLdap->connect();

/*
// Set up ldap handle and check EVERY TIME if user is authorized to be in this page via LDAP.
// This is to avoid potential session fixation / session hijacking or even XSS vulnerabilities
$sr = ldap_search($ldap, $config->ldap->basedn, sprintf($config->ldap->filter->admin, ldapspecialchars($login_username)), array('uid', 'userpassword'));
if(!$sr) die('Error searching!');
$info = ldap_get_entries($ldap, $sr);
Arcanum_Ldap::sanitize_entry_array($info);

if($info['count']!=1) {
    header("Location: signout.php?forced=2");
    exit;
}
unset($info);
 */

$msgs = array();

$uid = '';
if (isset($_REQUEST['uid'])) {
    $uid = $_REQUEST['uid'];
}
if (isset($_POST['dn'])) {
    $dn = $_POST['dn'];
}
/*
 * Post actions:
 *
 * Massive changes from search results:
 *
 * submitmassivechange
 * -> EITHER massivechangefilter: <ldap_filter>
 * -> OR     massivechangequery: <search_query>
 * ->   policy: nochange | default | <dn>
 * ->   lock: nochange | lock | unlock 
 * ->   forcechange: nochange | forcechange | unforcechange 
 *
 * Per user operations:
 * action_lock
 * action_unlock
 * action_force_pw_change
 * action_clear_pwdreset
 * subpolicy
 */


if (isset($_POST['submitmassivechange']) && (
    isset($_POST['massivechangefilter']) || isset($_POST['massivechangequery'])) ) {
    
    if(!empty($_POST['massivechangefilter'])) {
        $massivechangefilter = $_POST['massivechangefilter'];
    } else {
        $massivechangefilter = Arcanum_Ldap::constructFilterFromQuery($_POST['massivechangequery']);
    }

    $ldapmod = array();
    $ldapdel = array();

    $policy = $_POST['policy'];
    if ($policy != 'nochange') {
        if ($policy == 'default') {
            $ldapdel['pwdPolicySubentry'] = array();
        } else {
            $ldapmod['pwdPolicySubentry'] = urldecode($policy);
        }
    }

    $lock = $_POST['lock'];
    if ($lock != 'nochange') {
        if ($lock == 'lock') {
            $ldapmod['pwdaccountlockedtime'] = '000001010000Z';
        } elseif ($lock == 'unlock') {
            $ldapdel['pwdaccountlockedtime'] = array();
        }
    }

    $forcechange = $_POST['forcechange'];
    if ($forcechange != 'nochange') {
        if ($forcechange == 'forcechange') {
/*
            // for sun ds - TODO
            $sr1 = ldap_search(
                $ldap, $config->ldap->basedn, $filter,
                array('objectclass', 'userPassword')
            );

            $entries1 = ldap_get_entries($ldap, $sr);

            print_r($entries1);
            
            $original_user_password_value = $entries[0]['userPassword'];

            $ldapmod['userPassword'] = $original_user_password_value;
*/

            $ldapmod['pwdReset'] = 'TRUE';
        } elseif ($forcechange == 'unforcechange') {
            $ldapdel['pwdReset'] = array();
        }
    }

    if ($ldapmod || $ldapdel) {
        $sr = ldap_search(
            $ldap, $config->ldap->basedn, $massivechangefilter,
            array('objectclass', 'pwdPolicySubentry')
        );
        $entries = ldap_get_entries($ldap, $sr);

        $counters = array(
            'total' => $entries['count'],
            'modsuccess' => 0,
            'delsuccess' => 0,
            'delnochange' => 0,
            'error' => 0
        );

        if ($ldapmod) {
            for ($i=0; $i<$entries['count']; $i++) {
                if (ldap_modify($ldap, $entries[$i]['dn'], $ldapmod) === true) {
                    $counters['modsuccess']++;
                } else {
                    $counters['error']++;
                }
            }
        }

        if ($ldapdel) {
            
            for ($i=0; $i<$entries['count']; $i++) {
                if (@ldap_mod_del($ldap, $entries[$i]['dn'], $ldapdel) === true) {                    
                    $counters['delsuccess']++;
                } else {
                    $counters['delnochange']++;
                    // $counters['error']++;
                }
            }          
        }

        $t->assign('javascripts', $defaultJavascripts);

        $t->assign('counters', $counters);
                
        $t->display('html_header');
        $t->display('page_header_admin');

        $t->display('admin_massive_changes_results');

        $t->display('page_footer_admin');
        $t->display('html_footer');

        exit;
    }

}

$secondaryAccounts = array();

if (!empty($uid)) {
    if (isset($_POST['action_lock'])) {
        // Not implemented yet
        $action = 'lock';
        $ldapmod = array('pwdaccountlockedtime' => '000001010000Z');

    } elseif (isset($_POST['action_unlock'])) {
        $action = 'unlock';
        $ldapdel = array('pwdaccountlockedtime' => array());

    } elseif (isset($_POST['action_force_pw_change'])) {
        
        $action = 'force_pw_change';
        // $ldapmod = array('pwdReset' => 'TRUE');
        // for sun ds

        $filter = sprintf($config->ldap->filter->user, ldapspecialchars($uid));
        if ($restrict) {
            $filter = '(&'.$filter.$restrict['apply'].')';
        }

        $sr1 = ldap_search(
            $ldap, $config->ldap->basedn, $filter,
            array('objectclass', 'userPassword')
        );

        $entries1 = ldap_get_entries($ldap, $sr1);

        $original_user_password_value = $entries1[0]['userpassword'][0];

        $ldapmod['userpassword'] = $original_user_password_value;

        //  $ldapmod['pwdReset'] = 'TRUE';

    } elseif (isset($_POST['action_clear_pwdreset'])) {
        $action = 'clear_pwdreset';
        $ldapdel = array('pwdReset' => array() );
    
    } elseif (isset($_POST['subpolicy_submit'])) {
        $action = 'subpolicy';
        $subpolicy_post = $_POST['subpolicy'];

        // validation
        if( $subpolicy_post == 'default') {
            $subpolicy = 'default';
        } else {
            for($i=0; $i<$policies['count']; $i++) {
                if ($policies[$i]['dn'] == $subpolicy_post) {
                    $subpolicy = $subpolicy_post;
                    break;
                }
            }
        }
    
        if($subpolicy == 'default') {
            $ldapdel = array('pwdPolicySubentry' => array());
            
        } else {
            $ldapmod = array('pwdPolicySubentry' => $subpolicy);
            
        }
    }

    if (isset($action)) {
        // confirm, if needed, that we are allowed to change this user, and at the same time grab the dn
        if ($restrict) {
            $filter = '(&'.sprintf($config->ldap->filter->user, ldapspecialchars($uid)).$restrict['apply'].')';
        } else {
            $filter = sprintf($config->ldap->filter->user, ldapspecialchars($uid));
        }

        $sr = ldap_search(
            $ldap, $config->ldap->basedn, $filter,
            array_merge($config->admin->show_attrs->toArray(), array('objectclass'))
        );

        $count = ldap_count_entries($ldap, $sr);
        if ($count == 0) {
            Arcanum_Session::logout(LOGOUT_REASON_ACCESS_DENIED);
        } elseif ($count > 1) {
            // Multiple usernames? TODO
            Arcanum_Session::logout(LOGOUT_REASON_ACCESS_DENIED);
        }

        $entries = ldap_get_entries($ldap, $sr);

        $dn = $entries[0]['dn'];

        if (isset($ldapdel)) {
            $res = @ldap_mod_del($ldap, $dn, $ldapdel);
        } else {
            $res = @ldap_modify($ldap, $dn, $ldapmod);
        }

        if ($res === false) {
            $msgs[] = array('class' => 'error', 'msg' => sprintf( _("LDAP Error: %s"), ldap_error($ldap)));
        } else {
            $msgs[] = array('class' => 'success', 'msg' => _("Account has been modified successfully.") );
        }
    }

 $secondaryAccounts = array();

//---------
    if (!empty($config->ldap->secondary_accounts )) {

        $secondaryAccounts = $arcanumLdap->getSecondaryAccounts($uid);

/*       
         foreach($config->ldap->secondary_accounts->toArray() as $method => $ldapattr ) {
            $secondaryAccounts[$method] = strtolower($ldapattr);
        }
*/
    }
//------------
    $allattrs = array_merge($config->admin->show_attrs->toArray(), array('objectclass', $config->ldap->passwordAttribute),
                            array_keys($arcanumLdap->pwAttributes));

    $filter = sprintf($config->ldap->filter->user, ldapspecialchars($uid));
    if ($restrict) {
        $filter = '(&'.$filter.$restrict['apply'].')';
    }
    $sr = ldap_search($ldap, $config->ldap->basedn, $filter, array_merge($allattrs, array('+')));

	$entries = ldap_get_entries($ldap, $sr);
    Arcanum_Ldap::sanitize_entry_array($entries);



    if ($entries['count'] != 1) {
        $msgs[] = array('class' => 'warning', 'msg' => _("Username not found") );
    } else {

        $info = array('dn' => $entries[0]['dn']);
        foreach($allattrs as $a) {
            $attr = strtolower($a);
            if (isset($entries[0][$attr]) && $entries[0][$attr]['count'] > 0) {

                $info[$attr] = array();
                for($i=0; $i<$entries[0][$attr]['count']; $i++) {
                    $info[$attr][] = $entries[0][$attr][$i];
                }
            }
        }
        //$info = $entries[0];
    }
}


$exported_display_vars = array();
foreach($config->admin->summary_attrs->toArray() as $attr) {
    $exported_display_vars[$attr] = $arcanumLdap->attributes[$attr]['desc'];
}
$inlinejavascript = 'var summaryAttrs = '.json_encode($exported_display_vars) .';';

$enable_advanced_search = false;
if ($role == 'admin_policy') {
    // Enable advanced search
    $enable_advanced_search = true;
    $premade_filters = array(
        array(
            sprintf( $config->ldap->filter->user, '*'),
            _("All users")
        ),
        array(
            sprintf( $config->ldap->filter->user, 'a*'),
            _("Users with username starting with a")
        )
    );

    if(!empty($config->ldap->filter->user_receivesms)) {
        $premade_filters[] = array(
            sprintf( $config->ldap->filter->user_receivesms, '*'),
            _("Users that are allowed to receive SMS")     
        );
    }

    if($config->ldap->restrictfilters) {
        $tmp = $config->ldap->restrictfilters->toArray();
        if (!empty($tmp)) {
            foreach($tmp as $no => $rf) {
                $premade_filters[] = array(
                    '(&' . sprintf( $config->ldap->filter->user, '*' ) .  $rf['apply'] . ')',
                    $rf['description']
                );
                
            }
            unset($no);
            unset($rf);
        }
        unset($tmp);
    }
    $t->assign('premade_filters', $premade_filters);

}
$t->assign('enable_advanced_search', $enable_advanced_search);


// =================

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
$t->assign('policies', $policies);

$t->display('html_header');
$t->display('page_header_admin');

$t->assign('uid', $uid);

if (!empty($msgs)) {
    $t->assign('msgs', $msgs);
    $t->display('messages');
}

if (!isset($info)) {
    $t->display('admin_choose_user');

} else {
    
    $t->assign('show_pwdreset_form', $show_pwdreset_form);
    $t->assign('pwAttrs', $arcanumLdap->pwAttributes);
    $t->assign('secondaryAccounts', $secondaryAccounts);
    $t->assign('userinfo', $entries[0]);
    $t->assign('show_attrs', $config->admin->show_attrs->toArray());
    $t->assign('info', $info);
    
    $t->display('admin_show_user');
}


$t->display('page_footer_admin');
$t->display('html_footer');


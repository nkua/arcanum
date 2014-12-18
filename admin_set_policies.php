<?php
/**
 * Change password of other users (admin form).
 *
 * @package arcanum
 * @version $Id: admin_set_policies.php 5881 2012-10-26 07:05:34Z avel $
 */

$initLocation = 'admin_set_policies';
require_once('include/init.php');

$msgs = array();

$policy_dn = (isset($_REQUEST['policy_dn']) ? urldecode($_REQUEST['policy_dn']) : false);
$subsection = (isset($_REQUEST['subsection']) ? $_REQUEST['subsection'] : '');
if(!in_array($subsection, array('basic', 'advanced', 'unsupported'))) $subsection = 'basic';

$arcanumLdap = new Arcanum_Ldap();
$ldap = $arcanumLdap->connect();

if(isset($_POST['policy_apply'])) {
    $ldapmod = array();
    $ldapdel = array();
    foreach($arcanumLdap->policyAttributes as $a => $d) {
        $attr = strtolower($a);

        // special formats
        if(isset($d['format']) && $d['format'] == 'boolean') {
            // booleans
            if(isset($_POST[$attr]) && $_POST[$attr] != 'FALSE') {
                $ldapmod[$attr] = 'TRUE';
            } else {
                $ldapmod[$attr] = 'FALSE';
            }
            
        } elseif (isset($d['format']) && $d['format'] == 'duration') {
            // example:
            // dur_val_pwdexpirewarning => 2592000
            // dur_unit_pwdexpirewarning => seconds

            if(!isset($_POST['dur_val_'.$attr]) && !isset($_POST['dur_unit_'.$attr])) {
                continue;
            }
            $unit = $_POST['dur_unit_'.$attr];
            $value = $_POST['dur_val_'.$attr];
            
            switch($unit) {
                case 'minutes':
                    $res = $value * 60;
                    break;
                case 'hours':
                    $res = $value * 60 * 60;
                    break;
                case 'days':
                    $res = $value * 60 * 60 * 24;
                    break;
                case 'months':
                    $res = $value * 60 * 60 * 24 * 30;
                    break;
                case 'seconds':
                default:
                    $res = $value;
                    break;
            }
            unset($unit);
            unset($value);
            
            $ldapmod[$attr] = $res;

        } else {
            if(isset($_POST[$attr])) {
                if(empty($_POST[$attr])) {
                    $ldapmod[$attr] = 0;
                } else {
                    $ldapmod[$attr] = $_POST[$attr];
                }
            }
        }
    }

    if( ldap_modify($ldap, $policy_dn, $ldapmod) === false ) {
        $msgs[] = array('class' => 'error', 'msg' => sprintf(_("Policy Update failed &mdash; %s"), ldap_error($ldap)));
    } else {
        $msgs[] = array('class' => 'success', 'msg' => _("Policy Updated."));
    }
        
} elseif(isset($_POST['policy_create_default'])) {

    $containerobject = array(
        'objectclass' => 'organizationalUnit',
        'ou' => 'PPolicies'
    );

    $policyobject = array(
        'objectClass' => array(
            'person',
            'pwdPolicy',
            'top',
        ),
        'cn' => 'default',
        'pwdAttribute' => '2.5.4.35', /*userPassword*/
        'sn' => 'default',
        'pwdAllowUserChange' => 'TRUE',
        'pwdCheckQuality' => 0,
        'pwdExpireWarning' => 86400,
        'pwdFailureCountInterval' => 0,
        'pwdGraceAuthNLimit' => 0,
        'pwdInHistory' => 10,
        'pwdLockout' => 'TRUE',
        'pwdLockoutDuration' => 0,
        'pwdMaxAge' => 31536000*6, // 6 years!
        'pwdMaxFailure' => 0,
        'pwdMinAge' => 60,
        'pwdMinLength' => 8,
        'pwdMustChange' => 'TRUE',
        'pwdSafeModify' => 'FALSE',
    );


if(ldap_search($ldap, 'ou=PPolicies,'.$config->ldap->basedn,'(objectclass=*)') === false) {
    if( ldap_add($ldap, 'ou=PPolicies,'.$config->ldap->basedn, $containerobject) === false) {
        $msgs[] = array('class' => 'error', 'msg' => 'Could not create container object &mdash; ' . ldap_error($ldap));
    }

}

        if(ldap_add($ldap, 'cn=default,ou=PPolicies,'.$config->ldap->basedn, $policyobject) === false) {
            $msgs[] = array('class' => 'error', 'msg' => 'Could not create policy object &mdash; ' . ldap_error($ldap));
        }





}



$showallpolicyattrs = false;
if(isset($_GET['showallpolicyattrs'])) {
    $showallpolicyattrs = true;
}

$policies = $arcanumLdap->getPolicies();

$t->assign('policy_dn', $policy_dn);

if($policy_dn != false ) {
    for($index=0; $index<$policies['count']; $index++) {
        if($policy_dn == strtolower(str_replace(' ', '', $policies[$index]['dn']))) {
            $policy = $policies[$index];
            $t->assign('policy', $policy);
            break;
        }
    }
}

$t->assign('policyAttrs', $arcanumLdap->policyAttributes);

// ================= Display =================

$t->assign('msgs', $msgs);
$t->assign('showallpolicyattrs', $showallpolicyattrs );
$t->assign('policies', $policies);
$t->assign('subsection', $subsection);
$t->assign('policyAttrs', $arcanumLdap->policyAttributes);
$t->assign('javascripts', $defaultJavascripts);

$t->display('html_header');
$t->display('page_header_admin');

if(!empty($msgs)) {
    $t->assign('msgs', $msgs);
    $t->display('messages');
}


if ($policies['count'] > 0 ) {
    if($policy_dn === false) {
        // Show a list of policy objects
        $t->display('admin_set_policies_list_policies');
    } else {
        // Show specific policy form
        $t->display('admin_set_policies_policy_form');
    }
} elseif($policies['count'] == 0 ) {
    $t->display('admin_create_policy');
}

$t->display('page_footer_admin');
$t->display('html_footer');


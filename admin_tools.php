<?php
/**
 * @package arcanum
 * @version $Id: admin_tools.php 5628 2012-05-04 11:09:00Z avel $
 */

$initLocation = 'admin_tools';
require_once('include/init.php');

include_once('Zend/Ldap/Attribute.php');
include_once('include/Arcanum_Ldap_Attribute_Formatter.php');

$msgs = array();
$arcanumLdap = new Arcanum_Ldap();
$ldap = $arcanumLdap->connect();

/*
if(isset($_POST['apply'])) {
    if( ldap_modify($ldap, $dn, $ldapmod) === false ) {
        $msgs[] = array('class' => 'error', 'msg' => 'Ενημέρωση Πολιτικής Απέτυχε &mdash; ' . ldap_error($ldap));
    } else {
        $msgs[] = array('class' => 'success', 'msg' => 'Ενημέρωση Πολιτικής Επιτυχής');
    }
}

$sr = ldap_search($ldap, $config->ldap->basedn, '(objectclass=pwdpolicy)',
    array_merge(array('cn'), array_keys($arcanumLdap->policyAttributes)) );
    
$policies = ldap_get_entries($ldap, $sr);

if($policies['count'] == 0 ) {
    $msgs[] = array('class' => 'error', 'msg' => 'Δεν υπάρχουν ορισμένες πολιτικές κωδικών στον LDAP Server.');
}
 */



// ================= Display =================

$t->assign('msgs', $msgs);
$t->assign('javascripts', $defaultJavascripts);

$t->display('html_header');
$t->display('page_header');

$t->display('navigation_admin');

if(!empty($msgs)) {
    $t->display('alert_messages');
}

echo '<div class="row">';
echo '<div class="twelve columns">';

$t->display('admin_tools');

echo '</div></div>';

$t->display('page_footer');
$t->display('html_footer');


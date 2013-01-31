<?php
/**
 * @package arcanum
 * @version $Id: admin_options.php 5724 2012-06-12 13:59:16Z avel $
 */

$initLocation = 'admin_options';
require_once('include/init.php');

include_once('Zend/Ldap/Attribute.php');
include_once('include/Arcanum_Ldap_Attribute_Formatter.php');

$msgs = array();
$arcanumLdap = new Arcanum_Ldap();
$ldap = $arcanumLdap->connect();

if(isset($_POST['apply'])) {
    if( ldap_modify($ldap, $dn, $ldapmod) === false ) {
        $msgs[] = array('class' => 'error', 'msg' => sprintf(_("Policy Update failed &mdash; %s"), ldap_error($ldap)));
        /*
        print '<pre>';
        print_r($ldapmod);
        print '</pre>';
        print_r(ldap_error($ldap));
        // */
    } else {
        $msgs[] = array('class' => 'success', 'msg' => _("Policy Updated."));
    }
        
}


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

$t->display('admin_options');

echo '</div></div>';


$t->display('page_footer');
$t->display('html_footer');


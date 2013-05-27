<?php
/**
 * Ajax methods for the setup / installation routine
 *
 * @package Arcanum
 */


$initLocation = 'setup';

if(!isset($_GET['method'])) {
    exit;
}

if($_GET['method'] == 'ldap_test_connection') {

    // ==================================================================
    //
    // Test Connection to LDAP
    //
    // ------------------------------------------------------------------


    $host = (isset($_POST['host']) ? $_POST['host'] : '');
    $base = (isset($_POST['base']) ? $_POST['base'] : '');
    $bind = (isset($_POST['bind']) ? $_POST['bind'] : '');
    $password = (isset($_POST['password']) ? $_POST['password'] : '');

    $ldap = @ldap_connect($host);
    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);


    if(!$ldap) {
        echo json_encode( array('error' => 'Could not Connect', 'result' => 0));
        exit;
    }

    $res = @ldap_bind($ldap, $bind, $password);

    if(!$res) {
        echo json_encode( array('error' => ldap_error($ldap), 'result' => 0));
        exit;
    }

    echo json_encode( array('result' => 1));

}
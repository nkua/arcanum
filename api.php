<?php
/**
 * API entrypoint.
 *
 * @package arcanum
 * @version $Id: api.php 5823 2012-10-02 15:11:31Z avel $
 */

$initLocation = 'api';
require_once('include/init.php');

$msgs = array();

if(!isset($_GET['action'])) {
    fail('No action defined');
    exit;
}

$action  = $_GET['action'];

switch($action) {
case 'authorize_for_password_change':
    $login_servers_tmp = $config->login_servers->toArray();
    $cas_host = $config->cas->host;

    if(empty($login_servers_tmp) || empty($cas_host)) {
        fail('No Login Servers defined');
    }

    $login_servers = array_unique(array_filter(array_merge($login_servers_tmp, array($cas_host)), 'strlen'));

    if(!in_array($_SERVER['REMOTE_ADDR'], $login_servers)) {
        fail('Call not originated from login server; Exiting');
    }

    if(!isset($_GET['uid'])) {
        fail('Wrong parameter; expecting uid in GET arguments');
    }
    
    $tokenstore = new Arcanum_Token;

    $uid = $_GET['uid'];
    $arcanumLdap = new Arcanum_Ldap();
    $ldap = $arcanumLdap->connect();

    $admins_filter = '(|'.sprintf($config->ldap->filter->admin_password, ldapspecialchars($uid)).
        sprintf($config->ldap->filter->admin_policy, ldapspecialchars($uid)).')';

    $sr = ldap_search($ldap, $config->ldap->basedn, $admins_filter, array('uid'));

    if(ldap_count_entries($ldap, $sr) > 0) {
        fail('This reset password method is not allowed for admin accounts');
    }
    $sr = ldap_search($ldap, $config->ldap->basedn, sprintf($config->ldap->filter->user, ldapspecialchars($uid)), array('uid'));
    if(ldap_count_entries($ldap, $sr) !== 1) {
        fail('User not found');
    }

    $token = $tokenstore->generate_token();
    $tokenstore->set_token($token, $uid);

    echo $token;
    exit;

    break;

default:
    break;
}

function fail($msg = '') {
    header("Status: 405 Method Not Allowed");
    echo $msg;
    exit;
}


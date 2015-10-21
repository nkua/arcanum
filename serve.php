<?php
/**
 * API entrypoint.
 *
 * @package arcanum
 * @version $Id: api.php 5823 2012-10-02 15:11:31Z avel $
 */

$initLocation = 'api';
require_once('include/init.php');
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/include/Calculator.php';
require __DIR__ . '/include/Backdoor.php';
require __DIR__ . '/include/essential.php';

$msgs = array();

if(!isset($_GET['key'])) {
    fail('No key defined');
    exit;
}

$key  = $_GET['key'];
allowHosts($key);
header('Content-Type: application/json');

    $methods = new Backdoor();
	$Server = new JsonRpc\Server($methods);
	$Server->setObjectsAsArrays();
	$Server->receive();

function fail($msg = '') {
    header("Status: 405 Method Not Allowed");
    echo $msg;
    exit;
}


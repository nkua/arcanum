<?php
/**
 * Generate QR image
 *
 * @package arcanum
 * @version $Id: qrcode.php 5614 2012-04-04 09:43:52Z avel $
 */

if(!isset($_GET['operation'])) {
    exit;
}

$operation = $_GET['operation'];

include_once ("lib/phpqrcode/qrlib.php");

switch($operation) {
case 'qr_sms':

    $to = urldecode($_GET['to']);
    $content = urldecode($_GET['content']);

    // according to draft-wilde-sms-uri-20
    $output = 'sms:'.$to.'?body='.urlencode($content);

    Header("Content-Type: image/png");
    QRcode::png($output);

    break;

default:
    break;
}



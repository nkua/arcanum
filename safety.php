<?php
/**
 * 
 *
 * @package arcanum
 * @version $Id: safety.php 5850 2012-10-11 06:48:27Z avel $
 */
   
$initLocation = 'safety';
require_once('include/init.php');

// === Presentation Logic === 

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


$t->display('safety');

if($isAdmin) {
    $t->display('page_footer_admin');
} else {
    $t->display('page_footer');
}
$t->display('html_footer');

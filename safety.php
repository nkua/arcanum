<?php
/**
 * 
 *
 * @package arcanum
 * @version $Id: safety.php 5850 2012-10-11 06:48:27Z avel $
 */
   
$initLocation = 'safety';
require_once('include/init.php');

$pw_min_len = $config->password_strength_policy->PW_CHECK_MIN_LEN;
$pw_min_nonalpha = $config->password_strength_policy->PW_CHECK_MIN_NON_ALPHA;
$pw_check_min_uniq = $config->password_strength_policy->PW_CHECK_MIN_UNIQ;
$pw_min_consecutive_numbers = $config->password_strength_policy->PW_MIN_CONSECUTIVE_NUMBERS;



// === Presentation Logic === 

$t->assign('javascripts', $defaultJavascripts);
$t->assign('pw_min_len',$pw_min_len);
$t->assign('pw_min_nonalpha',$pw_min_nonalpha);
$t->assign('pw_check_min_uniq',$pw_check_min_uniq);
$t->assign('pw_min_consecutive_numbers',$pw_min_consecutive_numbers);

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

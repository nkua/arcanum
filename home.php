<?php
/**
 * 
 *
 * @package arcanum
 * @version $Id: home.php 5809 2012-09-20 09:06:12Z avel $
 */
   
$initLocation = 'home';
require_once('include/init.php');

// === Presentation Logic === 

$t->assign('javascripts', $defaultJavascripts);

$t->display('html_header');
$t->display('page_header');
$t->display('navigation_user');

$t->display('home');

$t->display('page_footer');
$t->display('html_footer');

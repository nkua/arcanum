<?php
/**
 * Exception handler
 *
 * @package arcanum
 * @version $Id: ExceptionHandler.php 5823 2012-10-02 15:11:31Z avel $
 */

/**
 * exception handler
 */
function Arcanum_ExceptionHandler(Exception $exception) {
    global $config, $login_username;


    $event = array();
    $event['errno'] 	= $exception->getCode();
    $event['message'] 	= $exception->getMessage();
    $event['errfile'] 	= $exception->getFile();
    $event['errline'] 	= $exception->getLine();
    $event['backtrace'] = $exception->getTraceAsString();

    // 1) Log the event
    Arcanum_Logger::log_system('exception|'.$event['message'] . '|'. $event['errfile'] . ':'. $event['errline'], LOG_WARNING);
        
    // 2) Display the error & exit
    global $loggedin, $baseuri, $language, $lang, $initLocation, $defaultJavascripts, $defaultStyles;

    $t = new Template;
    $t->assign('defaultStyles', $defaultStyles);
    $t->assign('javascripts', $defaultJavascripts);
    $t->assign('loggedin', $loggedin);
    $t->assign('baseuri', $baseuri);
    $t->assign('language', $language);
    $t->assign('lang', $lang);
    $t->assign('title', $config->title);
    $t->assign('subtitle', $config->subtitle);
    $t->assign('initLocation', $initLocation);
    $t->assign('styles', array());

    $t->assign('event', $event);
    
    $t->display('html_header');
    $t->display('page_header');
    $t->display('exception_event');
    $t->display('page_footer');
    $t->display('html_footer');
    exit;
}


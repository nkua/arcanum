<?php
/**
 * Various Functions
 *
 * @package arcanum
 * @version $Id: misc.php 5724 2012-06-12 13:59:16Z avel $
 */


/**
 * Calculate base URI
 * @return string
 */
function baseuri(){
    global $PHP_SELF, $rootDirPages;
    
    $dirs = array('|/tests/.*|');

    foreach($rootDirPages as $page) {
        if(strstr($PHP_SELF, '/'.$page)) {
            $baseuri = substr($PHP_SELF, 0, strpos($PHP_SELF, '/'.$page));
            break;
        }
    }

    /* No main file was found, proceed to directories. */
    if(!isset($baseuri)) {
        $repl = array('');
        $baseuri = preg_replace($dirs, $repl, $PHP_SELF);
    }

    if(!isset($baseuri)) die('Application error: could not determine base URL.');
    return $baseuri;
}

function arcanumSetupEmail() {
    global $config;

    include_once('Zend/Mail.php');
    include_once('Zend/Mail/Transport/Smtp.php');

    $smtpconfig = $config->mail->smtp->toArray();
    if(empty($smtpconfig['ssl'])) unset($smtpconfig['ssl']);
    if(empty($smtpconfig['auth'])) unset($smtpconfig['auth']);

    $transport = new Zend_Mail_Transport_Smtp($config->mail->host, $smtpconfig);
    Zend_Mail::setDefaultTransport($transport);
    Zend_Mail::setDefaultFrom($config->mail->from, $config->mail->fromComment);
    Zend_Mail::setDefaultReplyTo($config->mail->replyto);

}

/*
 * A function for making time periods readable
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     2.0.1
 * @link        http://aidanlister.com/2004/04/making-time-periods-readable/
 * @param       int     number of seconds elapsed
 * @param       string  which time periods to display
 * @param       bool    whether to show zero time periods
 */
function time_duration($seconds, $use = null, $zeros = false, $topmost = 0) {
    // Define time periods
    $periods = array (
        'years'     => 31556926,
        'Months'    => 2629743,
        'weeks'     => 604800,
        'days'      => 86400,
        'hours'     => 3600,
        'minutes'   => 60,
        'seconds'   => 1
        );

    // strings for gettext
    $datestrings = array(
        'years' => array( _('year'), _('years')),
        'months' => array( _('Month'), _('Months')),
        'weeks' => array( _('week'), _('weeks')),
        'days' => array( _('day'), _('days')),
        'hours' => array( _('hour'), _('hours')),
        'minutes' => array( _('minute'), _('minutes')),
        'seconds' => array( _('second'), _('seconds')),
    );
        
 
    // Break into periods
    $seconds = (float) $seconds;
    $segments = array();
    foreach ($periods as $period => $value) {
        if ($use && strpos($use, $period[0]) === false) {
            continue;
        }
        $count = floor($seconds / $value);
        if ($count == 0 && !$zeros) {
            continue;
        }
        $segments[strtolower($period)] = $count;
        $seconds = $seconds % $value;
    }
 
    // Build the string
    $string = array();
    foreach ($segments as $key => $value) {
        $segment_name = ($value == 1 ? $datestrings[$key][0] : $datestrings[$key][1]); 
        $string[] = $value . ' ' . $segment_name;

        if($topmost > 0 && sizeof($string) == $topmost) {
            break;
        }
    }
 
    return implode(', ', $string);
    
}




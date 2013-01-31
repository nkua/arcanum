<?php
/**
 * Arcanum Logger
 *
 * @package arcanum
 * @version $Id: Logger.php 5824 2012-10-04 13:26:35Z avel $
 */

/**
 * Arcanum Logger
 */
class Arcanum_Logger {
    /**
     * Log a user (browser) action
     */
    public static function log_user($msg) {
        syslog(LOG_NOTICE, 'arcanum::'.$msg.
            ',ip='.$_SERVER['REMOTE_ADDR'].','.
            'ua='.$_SERVER['HTTP_USER_AGENT']);
    }

    /**
     * Log an incoming sms event
     */
    public static function log_sms($phone = 0, $text = 0, $smsc = 0) {
        syslog(LOG_NOTICE, 'arcanum_sms::phone='.$phone.',text='.$text.',smsc='.$smsc);
    }

    /**
     * Log a system (PHP) event
     */
    public static function log_system($msg, $level = LOG_NOTICE) {
        syslog($level, 'arcanum::'.$msg);
    }
}


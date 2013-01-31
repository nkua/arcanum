<?php
/**
 * Methods for notifications feature
 *
 * @package arcanum
 * @version $Id: Notifications.php 5824 2012-10-04 13:26:35Z avel $
 */

/**
 * Class for arcanum notifications sent via e-mail
 */
class Arcanum_Notifications {
    /** Data file */
    const STATUS_FILE = 'data/password_expiry_reminders_status.json';

    public static function get_configured_notifications() {
        if(file_exists('config/notifications.php')) {
            $notifications = require('config/notifications.php');
        } else {
            $notifications = require('include/notifications.template.php');
        }
        return $notifications;
    }
    
    public static function get_current_status() {
        if(file_exists(self::STATUS_FILE)) { 
            $status = json_decode(file_get_contents(self::STATUS_FILE), true);
        } else {
            $status = array();
        }
        return $status;
    }

    /**
     * Get e-mail address from an ldap entry
     * @param array $entry
     * @return mixed E-mail address or boolean false
     */
    public static function get_notification_address(&$entry) {
        $mailaddress = false;
        if(isset($entry['mail'])) {
            $mailaddress = $entry['mail'][0];
        }
        return $mailaddress;
    }
}


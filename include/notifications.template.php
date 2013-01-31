<?php
/**
 * Default template configuration for password expiry notifications / reminders.
 *
 * Time cheat sheet
 * #      300   5 M                 #    604800   1 W
 * #     2700  45 M                 #   1814400   3 W
 * #     3600   1 H                 #   2419200   1 M
 * #    54000  15 H                 #  14515200   6 M
 * #    86400   1 D                 #  26611200  11 M
 *
 * @package arcanum
 * @version $Id: notifications.template.php 5961 2013-01-04 13:10:38Z avel $
 */

return array(
    array(
        'id' => 'info_in_a_year',
        'subject' => _("Information about the Expiration of your Password"), 
        'seconds_to_expiry' => 29030400, // 1 year
        'method' => 'email',
        'message' => 'notification_email_info_in_a_year',
    ),
    array(
        'id' => 'info_in_a_month',
        'subject' => _("Information about the Expiration of your Password"), 
        'seconds_to_expiry' => 2419200, // month
        'method' => 'email',
        'message' => 'notification_email_info_in_a_month',
    ),
    array(
        'id' => 'info_in_a_week',
        'subject' => sprintf( _("Your password expires in %s"), _("a week") ),
        'seconds_to_expiry' => 604800, // 1 week
        'method' => 'email',
        'message' => 'notification_email_info_in_a_week',
    ),
    array(
        'id' => 'info_in_two_days',
        'subject' => sprintf( _("Your password expires in %s"), _("two days") ),
        'seconds_to_expiry' => 86400*2, // 2 days
        'method' => 'email',
        'message' => 'notification_email_info_in_two_days',
    ),
);


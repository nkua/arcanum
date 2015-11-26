
<div class="row">
    <div class="span8 offset2">


        <h3><?= _("Active Notifications") ?></h3>

        <p><?= _("This table shows the active notifications and reminders that are being sent via e-mail to users, before their passwords have expired.") ?></p>

        <p><?= sprintf( _("To change these notifications, please edit the file %s. If the file does not exist, create it using this as template: %s."),
            '<tt>config/notifications.php</tt>',
            '<tt>include/notifications.template.php</tt>' ) ?></p>


        <br/>

        <table class="table table-striped">
        <tbody>
        <?php
        foreach($notifications as $n) {
            print '<tr><td><a href="admin_notifications.php?show_template='.$n['id'].'">'.$n['subject'].'</a></td>'.
            '<td>'.time_duration($n['seconds_to_expiry']).'</td>'.
            '<td>'.$counts[$n['id']].'</td></tr>';
        }
        ?>
    </tbody>
</table>

<?php

if(!$show_all) {
    echo '<p><a href="admin_notifications.php?show_all=1">'. _("Show Details per User") .'</a></p>';
}

?>
</div>
</div>


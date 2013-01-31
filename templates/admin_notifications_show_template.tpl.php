

<h3><?= _("Showing Notification Message") ?></h3>

<p><?= _("To change this message, please edit the file:") ?></p>

<pre>
templates/emails/notification_email_<?= $show_template ?>.local.tpl.php
</pre>


<p><?= sprintf( _("If the file does not exist, use the file %s as a template"),
    '<tt>templates/emails/notification_email_'.$show_template.'.tpl.php</tt>') ?></p>


<pre>
<?= htmlspecialchars(file_get_contents('templates/emails/notification_email_'.$show_template.'.tpl.php')) ?>
</pre>

<p><a href="admin_notifications.php"><i class="icon-arrow-left"></i> <?= _("Back to Notifications"?></a></p>

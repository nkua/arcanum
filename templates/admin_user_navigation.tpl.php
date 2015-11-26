
<br/>

<div class="tabbable">

<ul class="nav nav-tabs">

<li<?php echo ($initLocation == 'admin_show_user' ? ' class="active"' : '' ); ?>>
    <a href="admin_show_user.php?uid=<?php echo htmlspecialchars($userinfo['uid'][0]) ?>"><?php echo _("Account Information") ?></a>
</li>

<li<?php echo ($initLocation == 'admin_change_password' ? ' class="active"' : ''); ?>>
    <a href="admin_change_password.php?uid=<?php echo htmlspecialchars($userinfo['uid'][0]) ?>" ><?php echo _("Change Password") ?></a></li>

</ul>

</div>

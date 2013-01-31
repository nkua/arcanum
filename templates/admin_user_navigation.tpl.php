
<br/>

<div class="tabbable">

<ul class="nav nav-tabs">

<li<? echo ($initLocation == 'admin_show_user' ? ' class="active"' : '' ); ?>>
    <a href="admin_show_user.php?uid=<?= htmlspecialchars($userinfo['uid'][0]) ?>"><?= _("Account Information") ?></a>
</li>

<li<? echo ($initLocation == 'admin_change_password' ? ' class="active"' : ''); ?>>
    <a href="admin_change_password.php?uid=<?= htmlspecialchars($userinfo['uid'][0]) ?>" ><?= _("Change Password") ?></a></li>

</ul>

</div>

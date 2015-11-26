<form name="arcanum_userform_2" action="admin_change_password.php" method="GET" class="form-inline" style="display: inline;">
    <input name="uid" type="hidden" value="<?= $info['uid'][0] ?>" />
    <input name="changepass_uid" type="submit" class="btn btn-primary span4" value="<?= _("Change Password") ?>" />
</form>


<form name="arcanum_userform_1" action="admin_show_user.php" method="POST" class="form-inline" style="display: inline;">

    <input name="uid" type="hidden" value="<?= $info['uid'][0] ?>" />
    <input name="dn" type="hidden" value="<?= $info['dn'] ?>" />

    <input name="action_lock" type="submit" class="btn btn-danger span4" value="<?= _("Lock Account") ?>" />
<br/>
   <input name="action_unlock" type="submit" class="btn btn-success span4" value="<?= _("Unlock account") ?>" />
<br/>
    <input name="action_force_pw_reset" type="submit" class="btn btn-warning span4" value="<?= _("Change Password upon Next Login") ?>" />
<br/>
</form>



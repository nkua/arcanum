<h3><?= _("Change Password") ?></h3>
            
<form name="changepass" id="form_changepass" action="admin_change_password.php" method="post" class="form-horizontal">

   <input type="hidden" name="uid" value="<?= $userinfo['uid'][0] ?>"/> 
   <input type="hidden" name="dn" value="<?= urlencode($userinfo['dn']) ?>"/> 

<div class="container">

  <div class="row">
    <div class="span8 offset1">

      <div class="control-group">
        <label class="control-label" for="newpass0"><?= _("Enter Password:") ?></label>
        <div class="controls">
            <input type="password" name="newpass[0]" placeholder="<?= _("Password") ?>" id="newpass0" class="input-medium"  value="" size="15" /><br/> 
        </div>
        
        <label class="control-label" for="newpass1"><?= _("Verify") ?></label>
        <div class="controls">
            <input type="password" name="newpass[1]" placeholder="<?= _("Verify") ?>" id="newpass1" class="input-medium" value="" size="15" /><br/>
        </div>
      </div>

    </div>
  </div>


  <div class="row">
    <div class="span5 offset1">
        <input type="button" class="btn btn-info btn-small" onClick="UOA.arcanum.generatePass('<?= htmlspecialchars($userinfo['uid'][0]) ?>');"
            value="<?= _("Generate a Random Password") ?>" rel="tooltip" data-placement="bottom"
            title="<?= _("Click to automatically generate a random password, which will pass the password strength requirements and will be relatively easy to pronounce") ?>" />

      <?= _("New password will be:") ?>  <tt><span id="newpassshow"></span></tt><br/>

    <br/>
    <div id="newpassnotice" style="display:none;" class="alert alert-warning">
        <?= _("Please make sure that you have noted down the password, or have provided it to the user, before moving on.") ?>
        <br/>
    </div>
    </div>
  </div>


  <div class="row">
    <div class="span5 offset1">
        <fieldset>
            <label class="checkbox" for="expire_pass_immediately">
            <input type="checkbox" name="expire_pass_immediately" value="1" id="expire_pass_immediately" />
                <small><?= _("Temporary password &mdash; this password will work temporarily for logging on a web service, but user will be notified to change it immediately.") ?></small>
            </label>
        </fieldset>
    </div>
  </div>

  <div class="row">
    <div class="span5 offset2">
        <br/><input type="submit" class="btn btn-primary" name="changepass" value="<?= _("Change Password") ?>" />

    </div>
  </div>
  
</div>

</form>


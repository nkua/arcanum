

<div class="row">
<div class="span8 offset2">

<form name="policy_new_form" action="admin_set_policies.php" method="POST">
    <h2><?= _("There are no defined password policies on the LDAP Server.")?></h2>
    <p><?= _("Create a new policy with some predefined values:")?></p>
    
    <input type="submit" class="btn btn-primary" value="<?= _("Create New Policy") ?>" name="policy_create_default" />
</form>

</div>
</div>


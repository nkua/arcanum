<?php
/**
 * Configuration for this array:
 * 'restrictfilters' => array(
 *             array(
 *                 'id' => 'mechanics',
 *                 'description' => 'Students of mechanics',
 *                 'adminfilter' => '(uid=passadmin)',
 *                 'apply' => '(GUStudentDepartmentID=479)',
 *             ),
 *         ),
 */
?>

<h3><?= _("Password Administrators") ?></h3>

<p><?= _("In this page, you can define administrators who can manage users' passwords, per organizational unit or user group.") ?></p>
<ul>
    <li><?= _("Administrator(s) are defined according to the <em>Administration Filter</em>.") ?></li>
    <li><?= _("These administrators can change the password of the users, who are defined according to the <em>Apply to Users Filter</em>.") ?></li>
</ul>

<form name="arcanumsetupform" action="setup.php" method="POST">
<input type="hidden" name="submitstep" value="7_admins" />

<div id="current_rows">
</div>

<div id="new_row">
</div>


<input type="button" class="btn btn-info span4" id="create_form_row" value="<?= _("Add new Administrator(s)") ?>" />
<br/>

<?php

$this->display('setup_save_button');

?>

</form>

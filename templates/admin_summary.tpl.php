<?php
global $config;
?>

<form name="summary_form" id="summary_form" action="admin.php" method="POST">

<div class="container">

<div class="row">
<div class="span12">
    <h2 class="pull-left"><?= _("At a Glance") ?> &mdash; <tt><?= $config->ldap->host ?>/<?= $config->ldap->basedn ?></tt></h2>
    <div class="pull-right">
        <a href="admin.php?refresh=1" class="btn"><i class="icon-refresh icon"></i> <?= _("Refresh") ?></a>
    </div>
</div>

<div class="row">
<div class="span6">

<?php
if($restrict) {
    echo '<h2 style="text-align: center;">'.$restrict['description'].'</h2>';
}

?>

<table class="table table-striped">
<thead><tr><td colspan="2"><h3><?= _("Users") ?></h3></td></tr></thead>
<tbody>
<?php

foreach($summaries as $s => $v) {
    if($summariesDef[$s]['group'] != 'info') continue;
?>
    <tr><td style="text-align: right;">
        <?php
            if($v > 0 && isset($summariesDef[$s]['bad'])) echo '<span style="color: red;">'; else echo '<span>';
        ?>
        <a href="admin.php?show_<?= $s ?>"/><?= $summariesDef[$s]['desc'] ?></a></span>
        </td>
        
         <td><?= ($v == 1000 ? '1000+' : $v) ?></td>
         <td>
            <?php
            if(isset($summariesDef[$s]['fix']) && $v > 0) {
                echo '<input type="submit" class="nice small radius white button" value="'. _("Fix&hellip;") .'" name="fix_'.$s.'" />';
            }
            ?>
        </td>
     </tr>

<?
}
?>
</tbody>
</table>


<p style="text-align: right;"><i class="icon-search"></i> <a href="admin_show_user.php"><?= _("Search for users&hellip;") ?></a></p>

<?php
if(Arcanum_Util::areSecondaryAccountsActive()) {
?>

<table class="table table-striped">
<thead><tr><td colspan="2"><h3><?= _("Secondary Accounts") ?></h3></td></tr></thead>
<tbody>
<?php

foreach($summaries as $s => $v) {
    if($summariesDef[$s]['group'] != 'secondaryaccounts') continue;
?>
    <tr><td style="text-align: right;">
        <a href="admin.php?show_<?= $s ?>"/><?= $summariesDef[$s]['desc'] ?></a>
        </td>
         <td><?= ($v == 1000 ? '1000+' : $v) ?></td>
     </tr>

<?
}
}
?>
</tbody>
</table>




</div>
<div class="span6">


<table class="table table-striped">
<thead><tr><td colspan="3"><h3><?= _("Password Administrators") ?></h3></td></tr></thead>
<tbody>
<?php

foreach($summaries as $s => $v) {
    if($summariesDef[$s]['group'] != 'admins') continue;
?>
    <tr><td style="text-align: right;">
        <?php
            if($v > 0 && isset($summariesDef[$s]['bad'])) echo '<span style="color: red;">'; else echo '<span>';
        ?>
        <a href="admin.php?show_<?= $s ?>"/><?= $summariesDef[$s]['desc'] ?></a></span>
        </td>
        
         <td><?= ($v == 1000 ? '1000+' : $v) ?></td>
     </tr>

<?
}
?>

</tbody>
</table>



<table class="table table-striped">
<thead><tr><td colspan="2"><h3><?= _("Possible Problems") ?></h3></td></tr></thead>
<tbody>
<?php

foreach($summaries as $s => $v) {
    if($summariesDef[$s]['group'] != 'manage') continue;
?>
    <tr><td style="text-align: right;">
        <?php
            if($v > 0 && isset($summariesDef[$s]['bad'])) echo '<span style="color: red;">'; else echo '<span>';
        ?>
        <a href="admin.php?show_<?= $s ?>"/><?= $summariesDef[$s]['desc'] ?></a></span>
        </td>
        
         <td><?= ($v == 1000 ? '1000+' : $v) ?></td>
         <td>
            <?php
            if(isset($summariesDef[$s]['fix']) && $v > 0) {
                echo '<input type="submit" class="btn btn-warning btn-small" value="'. _("Fix&hellip;") .'" name="fix_'.$s.'" />';
            }
            ?>
        </td>
     </tr>

<?
}
?>

</tbody>
</table>

</div>

</div>
</div>

</form>

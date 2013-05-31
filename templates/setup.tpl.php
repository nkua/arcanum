<?php
$this->display('html_header');

if($editing_existing) {
    $this->display('page_header_admin');
    
}
?>

<div class="container">

<div class="row">
    <div class="span10">
            <h3><?= _("Setup Password Management Application") ?></h3>
    </div>
</div>

<?php
if($msgs) {
    $this->display('messages');
}
?>

<div class="row">
    <div class="span4">
        <div>

            <div class="nav panel" >
            <?= Arcanum_ViewHelper_Setup::navigation($operation, $editing_existing); ?>
            </div>

<?php
if($editing_existing) {
?>
            <p><?= _("The existing configuration file, config/config.php, is being edited.") ?></p>
            <p><?= _("To save your changes in each page, click &ldquo;Save&rdquo; at the end of each page.") ?></p>
    
<?php
} else {
?>
            
            <div>
            <form name="arcanummisc" class="nice" action="setup.php" method="POST">
            <input type="submit" class="btn btn-warning" name="disable_installer" value="<?= _("Disable Web Setup") ?>" />
            </form>
            
            <input type="submit" class="btn" name="reset_setup"
                 onclick="window.location = 'setup.php?destroy_session=1'; return;" value="<?= _("Cancel Changes and Restart Setup") ?>" />

            </div>
<?php
}
?>
        </div>
    </div>
    
    <div class="span8">


<?php

if($operation) {
    $this->display('setup_'.$operation);

    $this->display('setup_validation_highlight');
}
?>
    </div>
</div>

</div>
</div>

<div id="setup_bottom"></div>
<?php

$this->display('html_footer');

// ==================================================================
//
// Helper Functions
//
// ------------------------------------------------------------------

function f_val($val) {
    return htmlspecialchars($val);
}


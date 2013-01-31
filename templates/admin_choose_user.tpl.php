<br/>
<div class="span8 offset1">

<?php
if($enable_advanced_search) {
?>

<ul class="nav nav-tabs">
  <li id="nav_simplesearch"><a href="#"><?= _("Simple Search") ?></a></li>
  <li id="nav_advancedsearch"><a href="#"><?= _("Advanced Search") ?></a></li>
</ul>

<?php
}
?>

    <div id="simplesearch">
        <p><h3> <?= _("Search users by name, surname, username / e-mail, ID") ?></h3></p>

        <form class="form" id="bodysearchform">

            <i class="icon-search"></i>
            <input type="text" placeholder="<?= _("Search") ?>" name="bodysearchquery" size="15" id="bodysearchquery" value="" data-provide="typeahead" class="search-query input-xlarge" />


        </form>
    </div>

<?php
if($enable_advanced_search) {
?>

    <div id="advancedsearch">
        <p><h3> <?= _("Enter LDAP filter") ?></h3></p>

        <form class="form" id="advancedbodysearchform">

            <i class="icon-search"></i>
            <input type="text" name="advancedsearchquery" size="40" id="advancedsearchquery" placeholder="(uid=*)" value="" class="search-query input-xxlarge" />
             <input type="submit" name="advancedsearchdo" value="<?= _("Search") ?>" class="btn " />
<br/>
<br/>
<blockquote>
            <p><?= _("Copy one from these readily availabe filters for starters:") ?></p>

            <table class="table">
<?php
    foreach($premade_filters as $no => $pf) {
        echo '<tr><td>'.$pf[1]. '</td>'.
            '<td><tt>' .$pf[0] .'</tt></td>'.
            '<td><a href="#" onclick="$(\'#advancedsearchquery\').val(\''.$pf[0].'\');" class="premadefilter"><i class="icon-arrow-up"></i> ' . _("Copy") . '</a></td>'.
            '</tr>';
    }

?>
            </table>

</blockquote>

        </form>
    </div>


<?php
}
?>


</div>

</div>

<div class="span9" id="numericresult">
    <p><?= sprintf( _("Found %s user entries"), '<span id="numericresult_num" class="bignumber"></span>') ?> </p>
</div>

<div class="span10" id="bodysearchresults">
</div>

<div class="span10 well " id="massivechanges">
<form id="massivechangeform" method="post" target="admin_show_user.php" class="form-horizontal">
    <input type="hidden" id="massivechangefilter" name="massivechangefilter" value="" />
    <input type="hidden" id="massivechangequery" name="massivechangequery" value="" />
    <p><?= _("For the users listed above, you can massively change, in one go, the following attributes:") ?></p>
    <div class="control-group">
        <label class="control-label" for="policy"><?= _("Particular Policy") ?></label>
        <div class="controls">
            <select name="policy" id="policy">
<?php
                echo '<option value="nochange">' . _("No Change") .'</option>';
                echo '<option value="default">'. _("Default or Unspecified Policy") . '</option>';

            if($policies) {
                for($j=0; $j < $policies['count']; $j++) {
                    echo '<option value="'.$policies[$j]['dn'].'">Specific Policy: '.$policies[$j]['cn'][0].'</option>';
                }
            }

?>
        </select>
        </div>
     </div>

    <div class="control-group">
        <label class="control-label" for="lock"><?= _("Lock Account") ?></label>
        <div class="controls">
            <select name="lock" id="lock">
                <option value="nochange"><?= _("No Change") ?></option>';
                <option value="lock"><?= _("Lock All Accounts") ?> </option>
                <option value="unlock"><?= _("Unlock All Accounts") ?></option>
            </select>
        </div>
     </div>

    <div class="control-group">
        <label class="control-label" for="forcechange"><?= _("Force Password Change") ?></label>
        <div class="controls">
            <select name="forcechange">
                <option value="nochange"><?= _("No Change") ?></option>';
                <option value="forcechange"><?= _("All Accounts change password upon next login") ?></option>
                <option value="unforcechange"><?= _("Don't request password change upon next login") ?></option>
            </select>
        </div>
     </div>


    <div class="control-group">
        <div class="controls">
            <input type="submit" name="submitmassivechange" class="btn" value="<?= _("Apply These Changes") ?>" />
        </div>
    </div>
</form>

</div>

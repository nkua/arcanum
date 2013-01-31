
<div class="container">
    <div class="row">
        <div class="span3">
            <a href="admin_set_policies.php"><i class="icon-arrow-left"></i> <?= _("Back to Policies List") ?></a>
        </div>
    </div>

    <div class="row">
        <div class="span12">

<form name="policy_<?= $index ?>_form" id="policy_<?= $index ?>_form" action="admin_set_policies.php<?= ($showallpolicyattrs ? '?showallpolicyattrs=1' : '' ) ?>" method="POST">
<input type="hidden" name="policy_dn" value="<?= htmlspecialchars(urlencode($policy['dn'])) ?>" />
<input type="hidden" name="subsection" value="<?= $subsection ?>" />

<?php

if(!empty($policy['cn'])) {
    echo '<h3>'. sprintf("Policy &quot;%s&quot;", $policy['cn'][0]) . ' <tt>'.$policy['dn'].'</tt></h3>';
} else {
    echo '<h3>'. sprintf("Policy &quot;%s&quot;", $policy['dn']) . '</h3>';
}
?>

<ul class="nav nav-tabs">
<?php
echo '
    <li' .($subsection == 'basic' ? ' class="active"' : '' ) . '><a href="admin_set_policies.php?policy_dn='.htmlspecialchars(urlencode($policy_dn)).'&subsection=basic">' . _("Basic Policies") . '</a></li>
    <li' .($subsection == 'advanced' ? ' class="active"' : '' ) . '><a href="admin_set_policies.php?policy_dn='.htmlspecialchars(urlencode($policy_dn)).'&subsection=advanced">' . _("Advanced Policies") . '</a></li>
    <li' .($subsection == 'unsupported' ? ' class="active"' : '' ) . '><a href="admin_set_policies.php?policy_dn='.htmlspecialchars(urlencode($policy_dn)).'&subsection=unsupported">' . _("Unsupported Policies") . '</a></li>
    ';
?>
</ul>
    
<?php

if($subsection == 'unsupported')
   
echo '<p><span style="color: black; font-style: italics;">'.
        _("(*) Note: This attribute is not used by the supporting applications (Arcanum, CAS); however, it can be used by other third-party applications which also make use of this LDAP Password Policy.") .
        '</span></p>
';
?>

<table class="table table-striped table-bordered">
<thead>
    <tr><td>
        <?= _("Name") ?>
    </td><td>
        <?= _("Current Value") ?>
    </td><td>
        <?= _("New Value") ?>
    </td><td>
        <?= _("Description") ?>
    </td></tr>
</thead>

<tbody>
<?php

foreach($policyAttrs as $a => $d) {

    $attr = strtolower($a);

    $skip = false;
    switch($subsection) {
    case 'basic':
        if(!isset($d['important'])) $skip = true;
        break;
        
    case 'unsupported':
        if(!isset($d['beyondarcanum'])) $skip = true;
        break;

    case 'advanced':
        if(isset($d['important']) || isset($d['beyondarcanum'])) $skip = true;
        break;

    }


    if (!array_key_exists($attr, $policy) ||
      ( array_key_exists($attr, $policy) && ($policy[$attr]["count"] == 0))  ) {
          $val = '';
    } else {
          $val = $policy[$attr][0];
    }
    
    if($skip) {
        // skip this attribute from displaying
        echo '<input type="hidden" name="'.$attr.'" id="input_'.$attr.'" value="'.htmlspecialchars($val).'" /> ';
        continue;
    }


    echo '<tr><td class="attributeName">'.$d['desc'].' <small><tt>('.$attr.')</tt></small>';
    if(isset($d['beyondarcanum'])) {
        echo ' <strong>(*)</strong>';
    }
    echo '</td>';
    
    echo '<td class="attributeValue">';
    
    switch ($attr) {
    case 'pwdminage':
    case 'pwdmaxage':
    case 'pwdlockoutduration':	
    case 'pwdmindelay':	
    case 'pwdmaxdelay':	
    case 'pwdmaxidle':
    case 'pwdfailurecountinterval':
    case 'pwdexpirewarning':
        echo time_duration($val);
        break;

    case 'pwdinhistory':
        echo htmlspecialchars($val).' ' . _("passwords");
        break;

    case 'pwdcheckquality':
        switch($val) {
        case 1:
            echo _("Checked");
            break;
        case 2:
            echo _("Enforced");
            break;
        default:
            echo _("Not Enforced");
            break;
        }
        break;

    case 'pwdlockout':	
    case 'pwdmustchange':	
    case 'pwdallowuserchange':	
    case 'pwdsafemodify':	
        echo ($val === 'TRUE' ? 'Enabled' : 'Disabled');
        break;
    
    case 'pwdminlength':
    case 'pwdmaxlength':
        echo htmlspecialchars($val).' ' . _("characters");
        break;
        
    case 'pwdexpirewarning':
        echo htmlspecialchars($val).' ' . _("seconds before current password expires");
        break;
    
    case 'pwdmaxfailure':	
        echo htmlspecialchars($val).' ' . _("tries");
        break;

    case 'pwdgraceauthnlimit':	
        echo htmlspecialchars($val).' ' . _("times");
        break;

    case 'pwdgraceexpiry':	
    default:
        echo $val;
        break;
    }
    
    echo '</td>';

    
    echo '<td class="attributeValue">';

    switch ($attr) {
    case 'pwdminage':
    case 'pwdmaxage':
    case 'pwdlockoutduration':	
    case 'pwdmindelay':	
    case 'pwdmaxdelay':	
    case 'pwdmaxidle':
    case 'pwdfailurecountinterval':
    case 'pwdexpirewarning':
        Arcanum_ViewHelper_Policy::time_form($attr, $val);
        break;

    case 'pwdinhistory':
        echo '<input type="text" class="input-small" name="'.$attr.'" value="'.htmlspecialchars($val).'" /> ' . _("passwords");
        break;

    case 'pwdcheckquality':
        echo '<select class="input-small" name="'.$attr.'">
            <option value="0" ' . (($val != 1 && $val != 2) ? 'selected="SELECTED"' : '') . '>0 - ' ._("Not Enforced") .'</option>
            <option value="1" ' . ($val == 1 ? 'selected="SELECTED"' : '') . '>1 - ' ._("Checked") .'</option>
            <option value="2" ' . ($val == 2 ? 'selected="SELECTED"' : '') . '>2 - ' ._("Enforced") .'</option>
            </select>
            ';
        break;

    case 'pwdlockout':	
    case 'pwdmustchange':	
    case 'pwdallowuserchange':	
    case 'pwdsafemodify':	
        echo '<input type="checkbox" class="input-small" name="'.$attr.'" '.($val === 'TRUE' ? 'checked="CHECKED"' : '').' />';
        break;
    
    case 'pwdminlength':
    case 'pwdmaxlength':
        echo '<input type="text" class="input-small" name="'.$attr.'" value="'.htmlspecialchars($val).'" /> ' . _("characters");
        break;
        
    case 'pwdexpirewarning':
        echo '<input type="text" class="input-small" name="'.$attr.'" value="'.htmlspecialchars($val).'" /> ' . _("seconds before current password expires");
        break;
    
    case 'pwdmaxfailure':	
        echo '<input type="text" class="input-small" name="'.$attr.'" value="'.htmlspecialchars($val).'" /> ' . _("tries");
        break;

    case 'pwdgraceauthnlimit':	
        echo '<input type="text" class="input-small" name="'.$attr.'" value="'.htmlspecialchars($val).'" /> ' . _("times");
        break;

    case 'pwdgraceexpiry':	
    default:
        echo $val;
        break;
    }
    echo '</td><td>';
    
    echo $d['help'];

    if(isset($d['recommended'])) {
        echo "<br/><em>". sprintf( _("Recommended Value: %s"), $d['recommended']) . "</em>";
    }


    echo '</td>';
    echo '</tr>';

}
?>
<tr><td colspan="2">

    <input type="submit" class="btn btn-success" value="<?= _("Update Policy") ?>" name="policy_apply" />

</td></tr>
</tbody>
</table>
</form>

</div>
</div>
</div>


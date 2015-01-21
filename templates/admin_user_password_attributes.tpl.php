<?php
/**
 * Set the 'editable' variable to false to avoid displaying editing options
 */

if(!isset($editable)) $editable = true;

?>

<table class="table">
<thead><tr><td colspan="2"><h3><?= _("Password-related Attributes") ?><h3></td></tr></thead>
<tbody>
    
<tr><td>Password</td>

<td>
    <? // TODO
     if(isset($info['userpassword']) && isset($info['userpassword'][0])) {
        echo Arcanum_LdapAttributeFormatter::formatLdapPw($info['userpassword'][0]);
     } else {
        echo "Password not readable";
     }
     ?>
</td></tr>


<?php
foreach($pwAttrs as $attr => $d) {
    $a = strtolower($attr);
    echo '<tr><td title="'.$d['desc'].'">'.$d['title'].'</small></td>';
    echo '<td>';
    if(isset($info[$a])) {
        foreach($info[$a] as $value) {
            if(isset($d['formatter'])) {
                echo Arcanum_LdapAttributeFormatter::$d['formatter']($value);
            } else {
                echo htmlspecialchars($value);
            }
            echo '<br/>';
        }
    } else {
        if(isset($d['formatter'])) {
            echo Arcanum_LdapAttributeFormatter::$d['formatter']('');
        } else {
            echo '&mdash;';
        }
    }

    // Additional actions if we are editing
    if($editable) {

        switch($attr) {
        case 'pwdAccountLockedTime':
?>
<br/>
<form name="arcanum_userform_1" action="admin_show_user.php" method="POST" class="form-inline" style="display: inline;">

    <input name="uid" type="hidden" value="<?= htmlspecialchars($info['uid'][0]) ?>" />
    <input name="dn" type="hidden" value="<?= htmlspecialchars($info['dn']) ?>" />

    <input name="action_lock" type="submit" class="btn btn-danger" value="<?= _("Lock Account") ?>" />
   <input name="action_unlock" type="submit" class="btn btn-success" value="<?= _("Unlock account") ?>" />
</form>
<br/>
<?
            break;

        case 'pwdPolicySubEntry':
            if($policies) {
                echo '<form class="form-inline" action="admin_show_user.php" method="POST">';
                echo '<input name="uid" type="hidden" value="'.htmlspecialchars($info['uid'][0]).'" />';
                echo '<br/> Change to: <select name="subpolicy">';
                echo '<option value="default">'. _("Default or Unspecified Policy") . '</option>';

                for($j=0; $j < $policies['count']; $j++) {
                    echo '<option value="'.$policies[$j]['dn'].'">Specific Policy: '.$policies[$j]['cn'][0].'</option>';
                }
                echo '</select>';
                echo '<input type="submit" class="btn btn-small" name="subpolicy_submit" value="OK" />';
                echo '</form>';



            } else {
                echo _("Default or Unspecified Policy");
            }

            break;

        case 'pwdReset':
            if(true || $show_pwdreset_form === true) {
                
            echo '<br/>
                <form name="arcanum_userform_3" action="admin_show_user.php" method="POST" class="form-inline" style="display: inline;">
                <input name="uid" type="hidden" value="'.htmlspecialchars($info['uid'][0]).'" />
                <input name="action_force_pw_change" type="submit" class="btn btn-warning span4" value="'. _("Change password upon next login") .'" />
                <br/>
                <input name="action_clear_pwdreset" type="submit" class="btn btn-success span4" value="'._("Cancel compulsory password change") .'" />
                </form>';
            }

            break;

        default:
            break;

        }
    }

    echo '</td></tr>';
}
?>
</tbody>
</table>

<table class="table">
<thead><tr><td colspan="2"><h3><?= _("Secondary Accounts") ?><h3></td></tr></thead>
<tbody>
<?php

foreach($secondaryAccounts as $method => $ldapattr) {
    echo '<tr><td>';
    if($method == 'sms') {
        echo _("Mobile / SMS");
    } elseif($method == 'email') {
        echo _("E-Mail");
    } elseif($method == 'openid') {
        echo _("OpenID");
    }
    
    echo '</td><td>';
    if(isset($ldapattr)) {
        print htmlspecialchars($ldapattr) . '<br/>';
        //foreach($info[$ldapattr] as $val) {
        //    print htmlspecialchars($val) . '<br/>';
        //}
    } else {
        echo '&mdash;';
    }
    echo '</td></tr>';
}
?>
</tbody>
</table>


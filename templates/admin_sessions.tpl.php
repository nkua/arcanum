<div class="row">
<div class="span9 offset1">

<h3><?= _("Active Sessions") ?></h3>

<p><?= _("The following table shows who is using the password management web application at this moment.") ?></p>

<table class="table table-striped">
<thead>
<tr>
    <td></td>
    <td><?= _("Username") ?></td>
    <td><?= _("Access Level") ?></td>
    <td><?= _("Actions") ?></td>
</tr>
</thead>
<tbody>
<?php
$i = 1;

foreach($sessions as $s) {
    
    echo '<tr><td>'.$i.'</td>'.
        '<td>'. ( isset($s['login_username']) ? $s['login_username'] : _("Unknown") ) .'</td>'.
        '<td>';

    if(isset($s['isAdmin']) && $s['isAdmin'] === true) {
        echo '<strong>'. _("Administrator") . '</strong>';
    } else {
        if(isset($s['cleared_for'])) {
            if(isset($s['cleared_for']['passwordreset'])) 
                echo _("Password Reset") . '<br/>';
            if(isset($s['cleared_for']['myaccount']))
                echo _("My Account") . '<br/>';
        }
        if(isset($s['reset_password']) && $s['reset_password'] === true) {
                echo _("Password Recovery Procedure") . '<br/>';
        }
    }

    echo '</td>';

    echo '<td>';
    if(isset($s['login_username'])) {
        echo '<input type="button" name="" value="'. _("Details") .'" onclick="document.location=\'admin_show_user.php?uid='.$s['login_username'].'\';" class="btn btn-info" />';
    }

    echo '</td>'.
        '</tr>';
    $i++;
}
?>
</tbody>
</table>


<h3><?= _("Active Mail Tokens for Password Reset (Duration: 1 hour)") ?></h3>

<?php
print_token_table($active_mail_tokens);
?>

<h3><?= _("Active SMS Tokens for Password Reset (Duration: 10 minutes)") ?></h3>

<?php
print_token_table($active_sms_tokens);
?>

<?php

// Template Function definitions below

function print_token_table(&$active_tokens) {
    if(empty($active_tokens)) {
        echo '<p>' . _("There are no active tokens which were requested for immediate password change at this point.") . '</p>';
    } else {
        $i = 1;
        echo '
            <table class="table table-striped">
            <thead>
                <tr>
                    <td></td>
                    <td>'._("Username") . '</td>
                    <td>'._("Actions") . '</td>
                </tr>
            </thead>
            <tbody>
            ';
        foreach($active_tokens as $tok) {
            echo '<tr><td>'.$i.'</td><td>' . $tok['uid'] . '</td><td>';
            echo '<input type="button" name="" value="'._("Details") . '" onclick="document.location=\'admin_show_user.php?uid='.$tok['uid'].'\';" class="btn" />';
            echo '</td></tr>';
            $i++;
        }
        echo '</tbody>
            </table>
            ';
    }
}


?>

</div>
</div>


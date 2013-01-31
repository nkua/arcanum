
<h3><?= _("Users") ?></h3>

<p><?= _("The following table shows which users have already received some password expiry reminder notifications via e-mail.") ?></p>

<table class="table table-striped">
<?php
    
echo '<thead><tr><td></td><td>'. _("Username") . '</td>';
    foreach($notifications as $n) {
        echo '<td><small>'. $n['id'] .'</small></td>';
    }
echo '</tr></thead>
    <tbody>';

$i = 1;
foreach($status as $uid => $s) {
    echo '<tr><td>'.$i.'</td>'.
        '<td>'.$uid.'</td>';

    foreach($notifications as $n) {
        echo '<td>';
        if(isset($s[$n['id']]) && $s[$n['id']] == 'SENT') {
            echo '&#10003;';
        }
        echo '</td>';
    }
    echo '</tr>';
    $i++;
}

?>
</table>

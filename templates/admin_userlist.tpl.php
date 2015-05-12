<?php
$this->display('messages');

if(isset($pagination)) {
    $pagination_html = helper_get_pagination_html($pagination);
    echo $pagination_html;
}
?>

<table class="table table-striped arcanum-userlist span10">
<thead>qqq
<tr><td colspan="<?php echo sizeof($summary_attrs) + 2 ?>"> <h3><?php echo $userlist_title ?></h3> </td></tr>
<tr><td><?php echo _("#") ?></td>
<?php
    foreach($summary_attrs as $attr) {
        echo '<td>'. ((isset($arcanumLdap) && isset($arcanumLdap->attributes[$attr])) ? $arcanumLdap->attributes[$attr]['desc'] : $attr ) .'</td>';
    }
?>
<td><?php echo _("Actions") ?></td>
</tr>
</thead>
<tbody>
<?php
if(!isset($startnum)) {
    $startnum = 0;
}
for($i=0; $i<$entries['count']; $i++) {
    echo '<tr><td>'.($startnum + $i+1).'</td>';
    foreach($summary_attrs as $attr) {
        echo '<td>' . (!empty($entries[$i][$attr]) ? $entries[$i][$attr][0] : '' ) . '</td>';
    }
    echo '<td style="white-space: nowrap;">'.
        '<a class="btn btn-small" href="admin_show_user.php?uid='.htmlspecialchars($entries[$i]['uid'][0]).'"><i class="icon-user"></i> '._("Details") .'</a>
        <a class="btn btn-small" href="admin_change_password.php?uid='.htmlspecialchars($entries[$i]['uid'][0]).'"><i class="icon-lock"></i> '._("Change Password") .'</a>
        </td></tr>';
}

?>
</tbody>
</table>

<?php
if(isset($pagination)) {
    $pagination_html = helper_get_pagination_html($pagination);
    echo $pagination_html;
}


// ==================================================================
//
// Function Definitions of Helpers below
//
// ------------------------------------------------------------------

function helper_get_pagination_html(&$pagination) {
    global $currentpage;
    $ret = '<div class="pagination"><ul>';
    for($i=0; $i<sizeof($pagination); $i++) {
        
        $ret .= '<li '.( $pagination[$i] == $currentpage ? ' class="active"' : '' ) .' >'.
            '<a href="admin.php?show_users&amp;currentpage='.$pagination[$i].'&amp;p='.urlencode(json_encode($pagination)).'">'.($i+1).'</a></li>';
    }
    $ret .= '</ul></div>';
    return $ret;
}


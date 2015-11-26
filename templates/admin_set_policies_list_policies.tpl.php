<div class="container">
    <div class="row">
        <div class="span11">

            <h3><?= sprintf( _("%s Policies available in the server:"), $policies['count'] ) ?></h3>

            <table class="table table-striped">
                <thead>
                    <tr><td><?= _("Name") ?></td>
                        <td><?= _("dn") ?></td>
                        <td><?= _("# of users") ?></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>

                    <?php


                    for($index=0; $index<$policies['count']; $index++) {
                        $policy = &$policies[$index];
                        $policy_dn = strtolower(str_replace(' ', '', $policies[$index]['dn']));

                        echo '<tr><td>';

                        if(!empty($policy['cn'])) {
                            for($j=0; $j<$policy['cn']['count']; $j++) {
                                echo '<strong>' .  $policy['cn'][$j] . '</strong><br/>';
                            }
                        } else {
                            echo _("Unnamed Policy");
                        }

                        echo '</td><td>';
                        echo '<tt>'.$policy_dn.'</tt>';

                        echo '</td><td>';


                        echo '</td><td>';
                        echo '<a class="btn btn-info" href="admin_set_policies.php?policy_dn='.urlencode(htmlspecialchars($policy_dn)) .'">' . _("Show &amp; Edit") . '</a>';
                        echo '</td></tr>';

                        unset($policy);
                        unset($policy_dn);
                    }

                    ?>
                </tbody>
            </table>

        </div>
    </div>
</div>


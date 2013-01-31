
 <h3><img src="images/contact64.png" style="vertical-align: middle;" alt="" />
<?= $userinfo['cn'][0] ?></h3>


<table class="table">
<tbody>
<?php


foreach($show_attrs as $attr) {
    if($attr == 'cn') continue;

    echo '<tr><td class="attributeName" title="'.$attr.'" >'.$arcanumLdap->attributes[$attr]['desc'].'</td>';

    echo '<td class="attributeValue">';
    if (!array_key_exists($attr, $userinfo) ||
      ( array_key_exists($attr, $userinfo) && ($userinfo[$attr]["count"] == 0))  ) {
        echo '<br/>';
    } else {
        for ($x=0 ; $x < $userinfo[$attr]["count"] ; $x++) {
            switch ($attr) {
                default:
                    /* Use language attribute value, if it is available */
                    $attr_lang = $attr.';lang-'.$lang;
                    if(array_key_exists($attr_lang, $userinfo) &&
                      !empty($userinfo[$attr_lang][$x]) &&
                      ($userinfo[$attr_lang][$x] != " ") ) {
                        $val = $userinfo[$attr_lang][$x];
                    } else {
                        $val = $userinfo[$attr][$x];
                    }
                    echo htmlspecialchars($val) . "<br />";
            }

        }
    }
    echo '</td></tr>';
}
?>
</tbody>

</table>


<?php

if(!empty($msgs)) {
    echo '<div class="container"><div class="row"><div class="span8">';
    foreach($msgs as $m) {
        echo '<br/><div class="alert alert-'.$m['class'].'">'.$m['msg'].'</div><br/>';
    }
    echo '</div></div></div>';
}

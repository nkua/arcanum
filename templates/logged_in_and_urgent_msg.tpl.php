<?php
if(isset($urgentmsg)) {
?>
     <div class="urgentmsg">
     <div class="accountstatus"><?= $urgentmsg ?></div>
        <?php $this->display('logged_in_as');  ?>
     </div>

<?php
} else {
    $this->display('logged_in_as'); 
}

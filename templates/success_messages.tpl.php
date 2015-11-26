 
<?php
if(isset($success_title)) {
    echo '<h1>'.$success_title.'</h1>';
}
?>

	<div class="box-around">
 	    <div class="box-success">
        	
          <div class="title-line">
<?php
            foreach($msgs as $m) {
?>
        	<div class="title-msg">
                <?= $m['msg'] ?>
            </div>
<?php
            }
?>
            <div class="icon-msg"><img src="images/tick.png" /></div>
          </div>
        
        </div>
    </div>
   



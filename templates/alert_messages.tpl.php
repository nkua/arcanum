<?php

if(!empty($msgs)) {
?>
   <div class="box-around">
      <div class="box-fail">
      
 		 <div class="title-line">
<?php
            foreach($msgs as $m) {
?>
        	<div class="title-msg">
                <?php echo $m['msg'] ?>
            </div>
<?php
            }
?>
            <div class="icon-msg"><img src="images/fail.png" /></div>
         </div>
            
        </div>  
      </div>
<?php
}


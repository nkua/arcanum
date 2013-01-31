<?php

if(!empty($msgs)) {
?>
   <div class="box-around">
      <div class="box-fail">
      
 		 <div class="title-line">
<?
            foreach($msgs as $m) {
?>
        	<div class="title-msg">
                <?= $m['msg'] ?>
            </div>
<?
            }
?>
            <div class="icon-msg"><img src="images/fail.png" /></div>
         </div>
            
        </div>  
      </div>
<?
}


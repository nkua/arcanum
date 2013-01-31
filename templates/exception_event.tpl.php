<?php
/**
 * @package arcanum
 * @subpackage templates
 * @version $Id: exception_event.tpl.php 5823 2012-10-02 15:11:31Z avel $
 */
?>
   <div class="box-around">
      <div class="box-fail">
 		 <div class="title-line">
        	<div class="title-msg">

<p class="red"><strong><?= _("An error has been encountered:") ?></strong></p>
<br/>
<br/>
<br/>
<?php
if(!empty($event['message'])) {
    echo '<p class="exceptionmessage">'.$event['message'].'</p>';
}

if(!empty($event['errfile'])) {
    echo '<p>' . _("Technical information about this error:") . '<br/>';
    echo sprintf( _("File %s, line %s"),  '<tt>'.$event['errfile'].'</tt>' ,  '<tt>'.$event['errline'].'</tt>' );
    echo '<br/>';
    if(!empty($event['backtrace'])) {
        echo sprintf( _("Backtrace: %s") , '<tt>'. $event['backtrace'].'</tt>');
    }
    echo '</p>';

}
?>

    <p class="exceptionmessage"><?= _("Please press &quot;back&quot; in your browser and try again.") ?></p>

    <p><small><?= _("If the problem persists, contact your local administrator.") ?></small></p>

</div>
</div>
</div>
</div>

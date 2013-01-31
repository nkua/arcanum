<?php
/**
 * Page Footer, a place to put links to 
 *
 * @package change_password
 * @subpackage templates
 * @version $Id: page_footer.tpl.php 5954 2012-12-28 10:39:09Z avel $
 */

?>

</div> <!-- End of main-->
<div id="push"></div>
</div> <!--End of contentwrapper-->
<!-- Footer-->
<div id="footerwrapper">
  <div id="footerline"></div>
  <div id="footer">
    <div id="copyrightinfo"><?= $title ?> &mdash; <?= $subtitle ?></div>
    <div class="footerinfo">
      <?php
      if($terms_link) {
        echo '<a href="'.$terms_link.'" class="footerlink">'. _("Terms of Service") .'</a> |';
      }
      
      if($privacy_policy_link) {
        echo '<a href="'.$privacy_policy_link.'" class="footerlink">'. _("Privacy Policy") .'</a>';
      }
      ?>
    </div>
  </div><!--End of footer-->
</div><!--End of footerwrapper-->

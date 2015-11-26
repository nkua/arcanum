
<h3>Two-Factor Authentication</h3>

<p>Στο μέλλον εδώ θα υπάρχουν επιλογές σχετικά με το two-factor authentication με mobile app ή SMS.</p>

<input type="button" name="popup_symmetric" value="Εμφάνιση Επιλογών" class="nice big white button" />

<br/>
<br/>


<h3>Certificate Authentication</h3>

<p>Στο μέλλον εδώ θα υπάρχουν επιλογές σχετικά με certificate-based authentication.</p>

<input type="button" name="popup_symmetric" value="Εμφάνιση Επιλογών" class="nice big white button" />

<br/>
<br/>

<?php

/*
<h3>Αποκρυπτογράφηση Κωδικού</h3>

<p>Εδώ μπορείτε να ορίσετε το symmetric encryption key για την αποκρυπτογράφηση των password hashes. (Προαιρετικό).</p>

<input type="button" name="popup_symmetric" value="Εμφάνιση Επιλογών" class="nice big blue button" 
    onclick="$('#modal_symmetric').reveal();" />

<div id="modal_symmetric" class="reveal-modal">
     <form name="options_symmetric" method="POST" onsubmit="$(\'#modal_\'+this.name.substr(6)).trigger(\'reveal:close\'); return true;" action="">
     <h3>Επιλογές Αποκρυπτογράφησης Κωδικού</h3>
     Κλειδί Αποκρυπτογράφησης: <input name="symmetric_key" value="" />
     <br/>
     Μέθοδος: <select name="symmetric_method">

<?php
$methods = array(
'MCRYPT_3DES',
'MCRYPT_BLOWFISH',
'MCRYPT_DES',
'MCRYPT_RC6_128',
'MCRYPT_RC6_192',
'MCRYPT_RC6_256',
'MCRYPT_RIJNDAEL_128',
'MCRYPT_RIJNDAEL_192',
'MCRYPT_RIJNDAEL_256',
'MCRYPT_SERPENT_128',
'MCRYPT_SERPENT_192',
'MCRYPT_SERPENT_256',
'MCRYPT_TWOFISH_128',
'MCRYPT_TWOFISH_192',
'MCRYPT_TWOFISH_25',
);

foreach($methods as $method) {
    echo '<option value="'.$method.'">'.$method.'</option>';
}
?>
</select>   
<br/>

<input type="submit" name="save" class="nice small radius blue button"  value="Αποθήκευση" />
  </form>
 </div>
<br/>
 */
?>


<?php
if(!$editing_existing) {
?>

<h3><?= _("Choose Setup Language") ?></h3>

<select name="setup_lang" class="input" id="setupLang">
    <option value="en_US" <?= ($language == 'en_US' ? 'selected=""' : '') ?>>English</option>
    <option value="el_GR" <?= ($language == 'el_GR' ? 'selected=""' : '') ?>>Ελληνικά</option>
</select>

<?php
}
?>


<h3><?= _("Checklist") ?></h3>

<ul>
<li><?= _("LDAP server is installed and operational.") ?></li>
<li><?= sprintf( _("You have installed %s and %s in LDAP server"),
    '<a href="doc/ExtendedAuthConfig.ldif" target="_blank">ExtendedAuthConfig.ldif</a>',
    '<a href="https://spaces.internet2.edu/display/macedir/LDIFs" target="_blank">eduOrg, eduPerson schemas</a>') ?>


<?php

Arcanum_ViewHelper_Setup::example_accordion('include /etc/ldap/schema/ppolicy.schema
include /etc/ldap/schema/eduorg.schema
include /etc/ldap/schema/eduperson.schema
include /etc/ldap/schema/GUPerson.schema
include /etc/ldap/schema/GUExtendedAuthentication.schema',

sprintf( _("OpenLDAP Configuration example (%s)"),  '<tt>/etc/ldap/slapd.conf</tt>') );
?>


</li>
<li><?= _("You have set the proper LDAP ACLs and Policy Module Settings.") ?>

<?php

Arcanum_ViewHelper_Setup::example_accordion('
# Database Options

access to attrs=userPassword,shadowLastChange
        by dn="cn=manager,dc=org,dc=gr" write
        by anonymous auth
        by self write
        by * none

access to dn.base="" by * read

access to *
        by dn="cn=manager,dc=org,dc=gr" write
        by * none

overlay ppolicy
ppolicy_default "cn=students,ou=ppolicies,dc=org,dc=gr"
ppolicy_use_lockout
ppolicy_hash_cleartext
',

sprintf( _("OpenLDAP Configuration example (%s)"),  '<tt>/etc/ldap/slapd.conf</tt>') );
?>
</li>

</ul>

</ul>
<br/>

<?php
if(!$editing_existing) {
?>


<form name="arcanumsetupform" class="nice" action="setup.php" method="POST">
    <input type="hidden" name="submitstep" value="0_checklist" />
    <input type="submit" class="btn btn-primary span4"  name="save" value="<?= _("Proceed") ?>" />
</form>


<script language="javascript">
    $(function () {
        $('#setupLang').on('change', function (e) {
            window.location = 'setup.php?setlanguage='+this.value;
        });
    });
</script>

<?php
}
?>

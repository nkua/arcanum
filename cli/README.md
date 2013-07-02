CLI scripts
========

Just some scripts that were added for our convenience. They might be useful to you. Run them from the root arcanum directory.

----

    php cli/dectp.php <encoded_ctp>


Decrypts a symmetrically encrypted password

----

    php cli/hash_all_cleartext_passwords.php


Hashes, using the default hash algorithm (usually SSHA) all cleartext userPassword attributes in LDAP server.

----

    php cli/password_age_report.php

Prints out a report of the password age for all accounts.

----

    php cli/password_expiry_reminder [-q] [-d]

This script is to be run from cron, so as to send password expiry reminders to the users via e-mail.

Example crontab line:

    5 0 * * *   cd /var/www/arcanum && php5 cli/password_expiry_reminder.php -q

-q Is for quiet operation, -d for debug operation. Read the file source for more information.



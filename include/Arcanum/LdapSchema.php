<?php
/**
 * LDAP Attribute Schema.
 *
 * @package arcanum
 * @version $Id: LdapSchema.php 5966 2013-01-07 09:53:16Z avel $
 */

/**
 * The LdapSchema class is a simple structure that holds attribute names and descriptions.
 * It can be convenient for some front-end code that needs some LDAP attributes information 
 * but not an LDAP handle.
 */
class Arcanum_LdapSchema {
    public $attributes;
    public $pwAttributes;
    public $policyAttributes;

    function __construct() {
        global $config;

        $this->attributes = array(
            'schgracpersonid' => array(
                'desc' => _('AcademicID'),
                'title' => _('A unique identifier for a user of the entire organization. It is usually the department code concatenated with the registration number.'),
                'noneditable' => true,
            ),
          /*  'gustudentid' => array(
                'desc' => _('Student Registration #'),
                'title' => _('The student registration number.'),
                'important' => true,
                'editable' => true,
            ),*/
            'schGrAcDepartmentID' => array(
                'desc' => _('Department ID'),
                'title' => _('Numerical code that identifies the department.'),
                'important' => true,
                'editable' => true,
            ),
           /* 'GUStudentDepartment' => array(
                'desc' => _('Department Name'),
            ),*/
            'givenname' => array(
                'desc' => _('First Name'),
                'title' => _('First / given name; can contain second (middle) name'),
                'important' => true,
                'editable' => true,
            ),
            'sn' => array(
                'desc' => _('Last Name'),
                'title' => _('Last Name'),
                'important' => true,
                'editable' => true,
            ),
            'cn' => array(
                'desc' => _('Full Name'),
                'editable' => true,
            ),
           /* 'account_status' => array(
                'desc' => _("Account Status"), 
                'ldapattrs' => array('GUPersonAccountStatus'),
                'editable' => true,
                'title' => _("Whether your account is active or locked for some administrative reason"), 
                'defaultvalue' => 'enabled',
                'vocabulary' => array('', 'enabled', 'locked'),
            ),*/
            /*'student_status' => array(
                'desc' => _("Student Status"),
                'ldapattrs' => array('GUStudentStatus'),
                'title' => '',
            ),*/
            /*'gustudentsemester' => array(
                'desc' => _('Semester'),
                'title' => _('Semester number'),
                'editable' => true,
            ),*/
            'uid' => array(
                'desc' => _('Username'),
                'title' => _('Username or userid'),
                'editable' => true,
            ),
            
            'userpassword' => array(
                'desc' => _('Password'),
                'ldapattrs' => array('userPassword'),
                'compulsory' => true,
                'editable' => true,
            ),
            'edupersonprimaryaffiliation' => array(
                'desc' => _('Primary Affiliation'),
                'title' => _('The primary affiliation of this user in this educational organization (e.g. student, staff, faculty)'),
                'sameEverywhere' => true,
                'defaultvalue' => 'student',
            ),
            'title' => array(
                'desc' => _('Personal Title'),
            ),
            /*'gustudenttype' => array(
                'desc' => _('Student Type'),
                'title' => _('Whether the student is undergraduate, postgraduate, doctoral'),
                'vocabulary' => array('undergraduate', 'postgraduate', 'doctoral'),
                'defaultvalue' => 'undergraduate',
                'compulsory' => true,
                'editable' => true,
            ),*/
            'o' => array(
                'desc' => _('Organization'),
                'title' => _("Name of organization"),
                'sameEverywhere' => true,
                'defaultvalue' => '',
            ),
            'mail' => array(
                'desc' => _('E-Mail'),
                'editable' => true,
            ),
            'mailforwardingaddress' => array(
                'desc' => _('Alternative E-mails'),
                'title' => _("Alternative e-mail addresses where the user can receive e-mail messages."),
            ),
            'mailauthorizedaddress' => array(
                'desc' => _('Authorized E-mails'),
                'title' => _("Authorized e-mail addresses that user can use to send e-mail from."),
            ),
            'edupersonaffiliation' => array(
                'desc' => _('Affiliation'),
                'title' => _("Academic affiliation")
            ),
            'edupersonorgdn' => array(
                'desc' => _('Organization Distinguished Name'). '',
                'sameEverywhere' => true,
                'defaultvalue' => '',
            ),
            'businesscategory' => array(
                'desc' => _('Business Category'),
            ),
            'ou' => array(
                'desc' => _('Organizational Unit'),
            ),
        );

        $this->pwAttributes = array(
            'pwdChangedTime' => array(
                'title' =>  _("The last time the entry's password was changed"),
                'desc' => _("This attribute specifies the last time the entry's password was changed.  This is used by the password expiration policy.  If this attribute does not exist, the password will never expire."),
                'formatter' => 'formatLdapDate',
            ),
            'pwdAccountLockedTime' => array(
                'title' => _("Account locked"),
                'desc' => _("This attribute holds the time that the user's account was locked.  A locked account means that the password may no longer be used to authenticate.  A 000001010000Z value means that the account has been locked permanently, and that only a password administrator can unlock the account."),
                'formatter' => 'formatLdapPwAccountLockedTime',
            ), 
            'pwdFailureTime' => array(
                'title' => _("Authentication failures"),
                'desc' => _("This attribute holds the timestamps of the consecutive authentication failures."),
                'formatter' => 'formatLdapDate',
            ),
            'pwdHistory' => array(
                'title' => _("History of previously used passwords"),
                'desc' => _("This attribute holds a history of previously used passwords."),
                'formatter' => 'formatLdapPwHistory',
            ),
            'pwdGraceUseTime' => array(
                'title' => _("Timestamps of grace authentications"),
                'desc' => _("This attribute holds the timestamps of grace authentications after a password has expired."),
                'formatter' => 'formatLdapDate',
            ),
            'pwdReset' => array(
                'title' => _("Password updated by administrator"),
                'desc' => _("This attribute holds a flag to indicate (when TRUE) that the password has been updated by the password administrator and must be changed by the user."),
                'formatter' => 'formatpwdReset'
            ),
            'pwdPolicySubEntry' => array(
                'title' => _("Specific policy in effect"),
                'desc' => _("This attribute points to the pwdPolicy subentry in effect for this object."),
                'formatter' => 'formatPwPolicySubEntry',
            ),
            'pwdStartTime' => array(
                'title' => _("Password Start Time"),
                'desc' => _("This attribute specifies the time the entry's password becomes valid for authentication.  Authentication attempts made before this time will fail.  If this attribute does not exist, then no restriction applies."),
                'spec' => 'draft10',
            ),
            'pwdEndTime' => array(
                'title' => _("Password End Time"),
                'desc' => _("This attribute specifies the time the entry's password becomes invalid for authentication.  Authentication attempts made after this time will fail, regardless of expiration or grace settings.  If this attribute does not exist, then this restriction does not apply."),
                'spec' => 'draft10',
                'formatter' => 'formatLdapDate',
            ),
            'pwdLastSuccess' => array(
                'title' => _("Last successful authentication"),
                'desc' => _("This attribute holds the timestamp of the last successful authentication."),
                'spec' => 'draft10',
                'formatter' => 'formatLdapDate',
            )
        );

        $this->policyAttributes = array(
            'pwdMinAge' => array(
                'desc' => _('Minimum Age'),
                'format' => 'duration',
                'help' => _("This attribute holds the number of seconds that must elapse between modifications to the password.  If this attribute is not present, 0 seconds is assumed."),
                'important' => true,
            ),
            'pwdMaxAge' => array(
                'desc' => _('Maximum Age'),
                'format' => 'duration',
                'help' => _("This attribute holds the number of seconds after which a modified password will expire. If this attribute is not present, or if the value is 0 the password does not expire.  If not 0, the value must be greater than or equal to the value of the pwdMinAge."),
                'important' => true,
            ),
            'pwdInHistory' => array(
                'desc' => _('Passwords to keep in History'),
                'format' => '',
                'help' => _("This attribute specifies the maximum number of used passwords stored in the pwdHistory attribute. If this attribute is not present, or if the value is 0, used passwords are not stored in the pwdHistory attribute and thus may be reused."),
                'important' => true,
            ),
            'pwdCheckQuality' => array(
                'desc' => _('Enable Quality Check'),
                'format' => '',
                'help' => _("This attribute indicates how the password quality will be verified while being modified or added.  If this attribute is not present, or if the value is '0', quality checking will not be enforced.  A value of '1' indicates that the server will check the quality, and if the server is unable to check it (due to a hashed password or other reasons) it will be accepted.  A value of '2' indicates that the server will check the quality, and if the server is unable to verify it, it will return an error refusing the password."),
                'beyondarcanum' => true,
            ),
            'pwdMinLength' => array(
                'desc' => _('Minimum Length'),
                'format' => '',
                'help' => _("When quality checking is enabled, this attribute holds the minimum number of characters that must be used in a password.  If this attribute is not present, no minimum password length will be enforced.  If the server is unable to check the length (due to a hashed password or otherwise), the server will, depending on the value of the pwdCheckQuality attribute, either accept the password without checking it ('0' or '1') or refuse it ('2')."),
                'beyondarcanum' => true,
            ),
            'pwdMaxLength' => array(
                'desc' => _('Maximum Length'),
                'format' => '',
                'spec' => 'draft10',
                'help' => _("When quality checking is enabled, this attribute holds the maximum number of characters that may be used in a password.  If this attribute is not present, no maximum password length will be enforced.  If the server is unable to check the length (due to a hashed password or otherwise), the server will, depending on the value of the pwdCheckQuality attribute, either accept the password without checking it ('0' or '1') or refuse it ('2')."),
                'beyondarcanum' => true,
            ),
            'pwdExpireWarning' => array(
                'desc' => _('Expiration Warning Time'),
                'format' => 'duration',
                'help' => _("This attribute specifies the maximum number of seconds before a password is due to expire that expiration warning messages will be returned to an authenticating user. If this attribute is not present, or if the value is 0 no warnings will be returned.  If not 0, the value must be smaller than the value of the pwdMaxAge attribute."),
                'important' => true,
            ),
            'pwdGraceAuthNLimit' => array(
                'desc' => _('Number of times an expired password can be used'),
                'format' => '',
                'help' => _("This attribute specifies the number of times an expired password can be used to authenticate.  If this attribute is not present or if the value is 0, authentication will fail."),
            ),
            'pwdGraceExpiry' => array(
                'desc' => _('Grace authentications time'),
                'format' => '',
                'spec' => 'draft10',
                'help' => _("This attribute specifies the number of seconds the grace authentications are valid.  If this attribute is not present or if the value is 0, there is no time limit on the grace authentications."),
            ),
            'pwdLockout' => array(
                'desc' => _('Enable Lockout'),
                'format' => 'boolean',
                'help' => _("This attribute indicates, when its value is 'TRUE', that the password may not be used to authenticate after a specified number of consecutive failed bind attempts.  The maximum number of consecutive failed bind attempts is specified in pwdMaxFailure. If this attribute is not present, or if the value is 'FALSE', the password may be used to authenticate when the number of failed bind attempts has been reached."),
                'recommended' => 'TRUE',
            ),
            'pwdLockoutDuration' => array(
                'desc' => _('Lockout Duration'),
                'format' => 'duration',
                'help' => _("This attribute holds the number of seconds that the password cannot be used to authenticate due to too many failed bind attempts.  If this attribute is not present, or if the value is 0 the password cannot be used to authenticate until reset by a password administrator."),
                'recommended' => '0',
            ),
            'pwdMaxFailure' => array(
                'desc' => _('Maximum failures allowed'),
                'format' => '',
                'help' => _("This attribute specifies the number of consecutive failed bind attempts after which the password may not be used to authenticate. If this attribute is not present, or if the value is 0, this policy is not checked, and the value of pwdLockout will be ignored."),
                'recommended' => '0',
            ),
            'pwdFailureCountInterval' => array(
                'desc' => _('Interval for failure counter'),
                'format' => 'duration',
                'help' => _("This attribute holds the number of seconds after which the password failures are purged from the failure counter, even though no successful authentication occurred. If this attribute is not present, or if its value is 0, the failure counter is only reset by a successful authentication."),
            ),
            'pwdMustChange' => array(
                'desc' => _('Enable Force Change'),
                'format' => 'boolean',
                'help' => _("This attribute specifies with a value of 'TRUE' that users must change their passwords when they first bind to the directory after a password is set or reset by a password administrator.  If this attribute is not present, or if the value is 'FALSE', users are not required to change their password upon binding after the password administrator sets or resets the password.  This attribute is not set due to any actions specified by this document, it is typically set by a password administrator after resetting a user's password."),
                'recommended' => 'TRUE',
            ),
            'pwdAllowUserChange' => array(
                'desc' => _('Enable Password change by user'),
                'format' => 'boolean',
                'help' => _("This attribute indicates whether users can change their own passwords, although the change operation is still subject to access control.  If this attribute is not present, a value of 'TRUE' is assumed.  This attribute is intended to be used in the absence of an access control mechanism."),
            ),
            'pwdSafeModify' => array(
                'desc' => _('Require old password when changing'),
                'format' => 'boolean',
                'help' => _("This attribute specifies whether or not the existing password must be sent along with the new password when being changed.  If this attribute is not present, a 'FALSE' value is assumed."),
                'beyondarcanum' => true,
            ),
            'pwdMinDelay' => array(
                'desc' => _('Minimum Delay between auth attempts'),
                'format' => 'duration',
                'spec' => 'draft10',
                'help' => _("This attribute specifies the number of seconds to delay responding to the first failed authentication attempt.  If this attribute is not set or is 0, no delays will be used. pwdMaxDelay must also be specified if pwdMinDelay is set."),
            ),
            'pwdMaxDelay' => array(
                'desc' => _('Maximum Delay between auth attempts'),
                'format' => 'duration',
                'spec' => 'draft10',
                'help' => _("This attribute specifies the maximum number of seconds to delay when responding to a failed authentication attempt.  The time specified in pwdMinDelay is used as the starting time and is then doubled on each failure until the delay time is greater than or equal to pwdMaxDelay (or a successful authentication occurs, which resets the failure counter). pwdMinDelay must be specified if pwdMaxDelay is set."),
            ),
            'pwdMaxIdle' => array(
                'desc' => _('Maximum account idle time'),
                'format' => 'duration',
                'spec' => 'draft10',
                'help' => _("This attribute specifies the number of seconds an account may remain unused before it becomes locked.  If this attribute is not set or is 0, no check is performed."),
            ),
        );

        if($config->ldap->pwdpolicydraft10 !== true) {
            foreach($this->policyAttributes as $attr=>$d) {
                if(isset($d['spec']) && $d['spec'] == 'draft10') {
                    unset($this->policyAttributes[$attr]);
                }
            }
            foreach($this->pwAttributes as $attr=>$d) {
                if(isset($d['spec']) && $d['spec'] == 'draft10') {
                    unset($this->pwAttributes[$attr]);
                }
            }
        }
    }
}

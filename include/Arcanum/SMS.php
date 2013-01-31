<?php
/**
 * SMS Functions.
 *
 * @package arcanum
 * @version $Id: SMS.php 5823 2012-10-02 15:11:31Z avel $
 */

include_once('lib/sendsms/sendsms.class.php');
include_once('lib/sms_service_def/sms_service_def.inc.php');
include_once('lib/smsacct/SMSAcct.class.php');
include_once('lib/smsacct/SMSAcctNotification.class.php');

/**
 * Wrapper around our SMS libraries
 * @package arcanum
 */
class Arcanum_SMS {
    /** SendSMS instance */
    protected $sms;
    
    /** Accounting instance */
    protected $acct;
    
    /** Destination number */
    protected $number;
    
    /** Devel - debug mode */
    protected $debug = false;

    public function __construct($number) {
        global $config;

        if($config->devel->simulate_sms === true) {
            $this->debug = true;
        }

        $this->number = $number;

		$this->sms = new SendSMS($config->smsgw->toArray() );
        $this->acct = new SMSAcctNotification($config->smsacct->toArray(), $number, SMSCHP);
		$this->sms->SetDestinationNumber($number);

    }

    /**
     * Send message
     * @param string $message;
     */
    public function send($message) {
        if($this->debug === true) {
            Arcanum_Logger::log_user(sprintf('Simulated SMS sent; number: %s, message: %s', $this->number, $message));
            return;
        }
            
        $this->sms->SendText($message);
        
        $this->acct->setNotificationData(array(
            'message' => $message,
            'type'    => SMSMGMT_APP_NTF,
            'status'  => 'OK' // FIXME
            // 'status'  => $status
        ));
        if(($e = $this->acct->connect()) !== 0) {
            Arcanum_Logger::log_system('Could not connect to accounting db. Server returned '.$e, LOG_ALERT);
        }
        if(($e = $this->acct->write()) !== 0) {
            Arcanum_Logger::log_system('Could not write to accounting db. Server returned '.$e, LOG_ALERT);
        }
    }
    
}

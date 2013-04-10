<?php
/**
 * SMS Sender base class
 * @package arcanum
 * @version $Id$
 */

/**
 * SMS routine to be used in the application.
 * How to use:
 *
 * Sending by username: (only for jsonrpc method)
 *
 * <pre>
 * $mysms = Arcanum_SMS_Sender::Factory('username', $config->smsgw);
 * $mysms->send('This is a test message.');
 * </pre>
 *
 * Sending by mobile phone number: (for jsonrpc or kannel methods)
 *
 * <pre>
 * $mysms = Arcanum_SMS_Sender::Factory('6991324567', $config->smsgw);
 * $mysms->send('This is a test message.');
 * </pre>
 *
 * @package arcanum
 */
class Arcanum_SMS_Sender {
    /** Configuration (Zend Config instance) */
    protected $config;

    /** Destination (mobile number or recipient username) */
    protected $recipient;

    /** Log ID of message that we reply to. Optional. Can be used to build a
     * relationship between this outgoing text with an incoming one.
     */
    protected $log_id = null;


    /**
     * @Param string $recipient Username or mobile number of recipient
     * @param object $config Zend_Config instance of sms gateway configuration
     * @return void
     */
    public function __construct(&$recipient, &$config) {
        $this->recipient = $recipient;
        $this->config = $config;
    }

    /**
     * Factory. Builds an instance of an Arcanum_SMS_Sender
     *
     * @param string $recipient The destination mobile number or username
     * @param object $config Injection of the smsgw configuration
     * 
     */
    public static function Factory($recipient, &$config) {
        $senderclassname = 'Arcanum_SMS_Sender_'.ucfirst($config->sender);
        return new $senderclassname($recipient, $config);
    }

    public function setLogId($log_id) {
        $this->log_id = $log_id;
    }

   /**
     * Send message
     * @param string $message;
     */
    public function send($message) {
        if($this->config->simulate_sms === true) {
            Arcanum_Logger::log_user(sprintf('Simulated SMS sent; number: %s, message: %s', $this->number, $message));
            return;
        }
            
        return $this->_sendText($message);
    }
    
    /**
     * This method must be implemented by the subclass.
     * The return value must be either boolean true or, upon error, a string
     * with the error message.
     * @param string $message;
     */
    protected function _sendText($message) {
        return 'Not implemented';
    }

}


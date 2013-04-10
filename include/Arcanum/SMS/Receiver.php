<?php
/**
 * @package arcanum
 */

class Arcanum_SMS_Receiver {
    protected $mobile;
    protected $smsc;
    protected $text;
    protected $log_id = null;

    /**
     * Placeholder for future common constructing
     */
    public function __construct() {
        return;
    }

    /**
     * Getter for mobile number
     * @return string
     */
    public function getMobile() {
        return $this->mobile;
    }

    /**
     * Getter for SMS Center number
     * @return string
     */
    public function getSmsc() {
        return $this->smsc;
    }

    /**
     * Getter for Text Message
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Getter for Log ID
     * @return string
     */
    public function getLogId() {
        return $this->log_id;
    }

}

interface Arcanum_SMS_Receiver_Interface {
    public function __construct();

    /**
     * Read from environment the SMS input and store it in public properties:
     * $mobile, $sms, $text.
     */
    public function read();

}

class Arcanum_SMS_Receiver_InvalidRequestException extends Exception {}


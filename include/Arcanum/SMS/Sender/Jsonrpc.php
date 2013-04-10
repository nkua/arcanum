<?php
/**
 * @package arcanum
 * @version $Id$
 */

class Arcanum_SMS_Sender_Jsonrpc extends Arcanum_SMS_Sender {
    
    public function __construct(&$recipient, &$config) {
        parent::__construct($recipient, $config);
    }

    /**
     * @param string $message Message to send
     * @return mixed Boolean true upon success, or string with error message upon error.
     */
    protected function _sendText($message) {
        if(isset($this->config->institution) && !empty($this->config->institution)) {
            // We are sending an sms to a user by their username.
            // The SMS web service will do the hard work.
            $method = 'send_by_uid';
            $request = array(
                'uid' => $this->recipient,
                'message' => $message,
                'institution' => $this->config->institution,
                // 'simulate' => true
            );

        } else {
            // We are sending an sms to a user by their mobile number
            $method = 'send';
            $request = array(
                'number' => $this->recipient,
                'message' => $message,
                // 'simulate' => true
            );
        }

        if(!is_null($this->log_id)) {
            $request['log_id'] = $this->log_id;
        }

        $url = $this->config->host . $this->config->uri;

        try {
            $ret = jsonrpccall($url, $method, $request);
        } catch (Exception $e) {
            // Failure; message was not sent
            return $e->getMessage();
        }

        // Success; Message sent
        return true;

    }

}

// ==================================================================
//
// Function Defintions Below
//
// ------------------------------------------------------------------

/**
 * Performs a jsonRCP request and gets the results as an array
 *
 * @param string $url
 * @param string $method
 * @param array $params
 * @return array
 */
function jsonrpccall($url, $method, $params) {
    $currentId = 1;
    
    // prepares the request
    $request = array(
        'jsonrpc' => "2.0",
        'method' => $method,
        'params' => $params,
        'id' => $currentId
    );
    
    $request = json_encode($request, JSON_FORCE_OBJECT);
    
    // performs the HTTP POST
    $opts = array ('http' => array (
        'method'  => 'POST',
        'header'  => 'Content-type: application/json',
        'content' => $request
    ));
    $context  = stream_context_create($opts);
    if ($fp = fopen($url, 'r', false, $context)) {
        $response = '';
        while($row = fgets($fp)) {
            $response.= trim($row)."\n";
        }
        $response = json_decode($response,true);
    } else {
        throw new Exception('Unable to connect to URL');
    }
    
    // final checks and return
    // check
    if ($response['id'] != $currentId) {
        throw new Exception('Incorrect response id (request id: '.$currentId.', response id: '.$response['id'].')');
    }
    
    if (isset($response['error'])) {
        throw new Exception($response['error']['code'] . ' ' . $response['error']['message']);
    }
    
    return $response['result'];
}


<?php

require_once('SMS.interface.php');

class iTagg_SMS implements SMS {

    private $api_url;
    private $api_username;
    private $api_password;

    //Allowed characters
    private $gsm_charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz01234567889@?�_!1$"�#�?��%�&�\�(�)*:�+;����,<L�l��-=����.>����/� \' ';

    /** An array of recipients of the SMS */
    private $recipients;

    /** The sender **/
    private $sender;

    /** The contents of the message **/
    private $message;

    /** The reference **/
    private $reference;

    /** The response from the API **/
    public $response;


    /**
     * Creates a new iTagg_SMS object
     *
     * @param string $username iTAGG username
     * @param string $password iTAGG password
     */
    function __construct($username = '', $password = '') {
            $this->recipients = array();
            $this->api_url = 'http://secure.itagg.com/smsg/sms.mes';
            $this->sender = '';
            $this->api_username = $username;
            $this->api_password = $password;
    }


    /**
     * Adds a recipient to the SMS
     * @param $recipient The recipient to add to the SMS
     */
    function addRecipient($recipient) {
            //Check the recipient isn't in the array
            if (!in_array($recipient, $this->recipients)) {
                    //Add the recipient
                    $this->recipients[] = $recipient;
            }
    }


    /**
     * Removes a recipient from the SMS
     * @param $recipient The recipient to remove from the SMS
     */
    function removeRecipient($recipient) {
            foreach($this->recipients as $key => $recipient2) {
                    if($recipient == $recipient2) {
                            unset($this->recipients[$key]);
                    }
            }
    }


    /**
     *
     * Sets the sender of the SMS
     * @param unknown_type $sender
     */
    function setSender($sender) {
    	$this->sender = $sender;
    }


    /**
     *
     * Returns the sender of the SMS
     */
    function getSender() {
    	return $this->sender;
    }


    /**
     * Returns an array of recipients
     */
    function getRecipients() {
    	return $this->recipients;
    }


    /**
     * Sets the SMS message
     */
    function setMessage($message) {
        //Check the message is ok
        if($this->_checkMessage($message)) {
            $this->message = $message;
            return true;
        } else {
            return false;
        }
    }


    /**
     * Gets the SMS message
     */
    function getMessage() {
    	return $this->message;
    }


    /**
     * Sets the reference
     * The message will be tagged at iTagg with this reference, allowing it to be searched for/billed/etc
     * @param ref The reference
     */
    function setReference($ref) {
    	$this->reference = $ref;
    }


    /**
     * Sends the SMS
     */
    function send() {
    	//Build up an array of post items
    	$params = array();
    	$params['usr'] = $this->api_username;
    	$params['pwd'] = $this->api_password;
    	$params['from'] = $this->sender;
    	$params['to'] = implode(',', $this->recipients);
    	$params['type'] = 'text';
    	$params['route'] = $this->_getRoute($this->recpients[0]);
    	$params['txt'] = $this->message;
    	$params['userdef'] = $this->reference;

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $this->api_url);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    	$returned = curl_exec($ch);

    	/*$returned = 'error code|error text|submission reference
    	 0|sms submitted|a1e3fab42c90a7221c2d5501fd7b52ff-3';*/
    	$returned_lines = explode("\n", $returned);
    	$response = explode('|', $returned_lines[0]);
    	$this->response['status'] = $response[2];
    	$this->response['message'] = $response[3];
    	$this->response['reference'] = $response[4];
    }


    /**
     * Works out whether the message has been send or not
     *
     * @return boolean True if sent, else false
     */
    function hasBeenSent() {
        if ($this->response['status'] == 0) {
            return true;
        } else {
            return false;
        }
    }



    // ***** Helper functions ***** //

    /**
     * Works out which route to use
     *
     * @param string $number The phone number the message is being sent to
     *
     * @return number The route number to be used
     */
    function _getRoute($number) {
    	if (substr($number, 0, 1) == '0') {
    		return 7;
    	} else {
    		return 8;
    	}
    }


    /**
     *
     * Checks that a message only contains valid characters
     * @param unknown_type $message The message to check
     * @return true if the message is valid, otherwise false
     */
    function _checkMessage($message) {
        $charset = str_split($this->gsm_charset, 1);
        for($i; $i<strlen($message); $i++) {
            if (!in_array($message[$i], $charset)) {
                return false;
            }
        }

        return true;
    }
}


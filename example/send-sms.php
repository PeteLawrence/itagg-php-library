<?php

//Require the iTagg_SMS class
require('../src/iTagg_SMS.class.php');

//iTAGG username and password
$itaggUsername = '<iTAGG username>';
$itaggPassword = '<iTAGG password>';


//Read in command line options, and check that a mewssage and phone number were specified
$opts = 'm:n:';
$options = getopt($opts);

if (!isset($options['m'])) {
    die ('No message (-m) was specified');
}

if (!isset($options['n'])) {
    die ('No phone number (-n) was specified');
}


//Everything is OK, so prepare the message
$sms = new iTagg_SMS($itaggUsername, $itaggPassword);

//Add the recipient to the message
$sms->addRecipient($options['n']);

//Set the message text
$sms->setMessage($options['m']);

//Set the name of the sender
$sms->setSender('Nagios');

//Set the sender reference
$sms->setReference('nagios');

//Send the message
$sms->send();

//Check the message sent
if (!$sms->hasBeenSent()) {
	echo 'Error sending SMS';
}


?>
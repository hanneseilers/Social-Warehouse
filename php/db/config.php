<?php

$GLOBALS['dbDatabase'] = "35110m24661_5";
$GLOBALS['dbUser'] = "35110m24661_5";
$GLOBALS['dbPassword'] = "tjDGPjFIOz7zpH3rPdcR";
$GLOBALS['dbHost'] = "localhost";
$GLOBALS['log_enabled'] = false;

// mail address registered at providers webspace.
$GLOBALS['mail_from'] = "mail@domain.com";

/**
 * Sends mail.
 * @param string $from		Mail address where to send from
 * @param string $to		Mail address where to send message
 * @param string $subject	Mail subject
 * @param string $message	Mail message (text/html)
 */
function send_mail($from, $to, $subject, $message){
	
	// build mail header
	$header = array();
	$header[] = "MIME-Version: 1.0";
	$header[] = "Content-type: text/html; charset=iso-8859-1";
	$header[] = "Date: " . date('r');
	$header[] = "Message-ID: <" . md5(uniqid(microtime())) . "@" . $_SERVER['SERVER_NAME'] . ">";
	$header[] = "From: " . $from;
	$header[] = "Reply-To: " . $from;
	$header[] = "Return-Path: " . $from;
	$header[] = "X-Mailer: PHP/" . phpversion();
	$header[] = "X-Sender-IP: " . $_SERVER['REMOTE_ADDR'];
	
	// send mail
	mail($to, $subject, $message, implode("\r\n", $header), "-f " . $GLOBALS['mail_from']);
	
}

?>
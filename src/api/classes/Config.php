<?php

/**
 * Class holding configuration
 * @author H. Eilers
 *
 */
class Config{
	
	public static $dbHost = "localhost";				// hostname of mysql server
	public static $dbDatabase = "Social-Warehouse";		// name of database
	public static $dbPrefix = "sw_";					// prefix of database tables
	public static $dbUser = "root";						// user for database login
	public static $dbPassword = "password";				// password for database login
	public static $sqlLogEnabled = false;				// set true to enable logger output of sql commands

	public static $mailFrom = "mail@domain.com"	;		// valid, real mail address to send mails from
	public static $sessionTimeout = 1800;				// seconds until session expires
	
	/**
	 * Sends a mail.
	 * @param string $from		Mail address where to send from
	 * @param string $to		Mail address where to send message
	 * @param string $subject	Mail subject
	 * @param string $message	Mail message (text/html)
	 */
	public static function sendMail($from, $to, $subject, $message){
	
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
		mail($to, $subject, $message, implode("\r\n", $header), "-f " . Config::$mailFrom);
	
	}
	
}


?>
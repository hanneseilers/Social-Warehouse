<?php

include realpath(dirname(__FILE__)).'/logger/Logger.php';

/**
 * Logger class.
 * Offers direct console loggin including linebreak tag.
 * @author H. Eilers
 *
 */
class Log{
	
	private static $logger = null;
	
	private function __construct(){}
	
	/**
	 * Logs a message and adds <br /> html tag.
	 * @param string $level		Log level
	 * @param string $message	Log message
	 */
	public static function log($level, $message){
		// check if to create new logger instance
		if( Log::$logger == null ){
			Logger::configure( realpath(dirname(__FILE__)).'/log.xml' );
			Log::$logger = Logger::getLogger( 'main' );
		}
		
		// call log function in logger instance
		call_user_func( array(Log::$logger, $level), $message );
	}
	
	/**
	 * Logs an object as json string and adds <br /> html tag.
	 * @param string $level		Log level
	 * @param string $obj		Object
	 */
	public static function jlog($level, $obj){
		Log::log( $level, json_encode($obj) );
	}
	
	/**
	 * Automatik logging.
	 * Selects if to log as json string or as plain string depending if
	 * message is object or string.
	 * @param string $level
	 * @param mixed $message
	 */
	private static function autolog($level, $message){
		if( is_string($message) )
			Log::log( $level, $message );
		else
			Log::jlog( $level, $message );
	}
	
	/**
	 * Shows debug log message
	 * @param mixed $message
	 */
	public static function debug($message){
		Log::autolog( 'debug', $message );
	}
	
	/**
	 * Shows info log message
	 * @param mixed $message
	 */
	public static function info($message){
		Log::autolog( 'info', $message );
	}
	
	/**
	 * Shows warning log message
	 * @param mixed $message
	 */
	public static function warn($message){
		Log::autolog( 'warn', $message );
	}
	
	/**
	 * Shows error log message
	 * @param mixed $message
	 */
	public static function error($message){
		Log::autolog( 'error', $message );
	}
	
}

?>
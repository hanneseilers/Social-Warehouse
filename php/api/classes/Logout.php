<?php

/**
 * Class for api logout request.
 * @author H. Eilers
 *
 */
class Logout extends Request{
	protected $sid = "";
	
	/**
	 * Constructor
	 * @param integer $session	Session ID
	 */
	public function __construct($session){
		parent::__construct( "logout" );
		$this->sid = $session;
	}
	
	/**
	 * Destroys session
	 * @return true if session destroyed, false otherwise.
	 */
	public function logout(){
		$session = new Session( $this->sid );
		if( $session->sessionId > 0 )
			return $session->destroy();
		return false;
	}
	
	/**
	 * Checks if object is instance of Logout class.
	 * @param mixed $object	Object to check
	 * @return true if is Logout class object, false otherwise.
	 */
	public static function isInstance($object){
		if( parent::isInstance($object)
				&& property_exists($object, 'sid') )
			return true;
		return false;
	}
}

?>
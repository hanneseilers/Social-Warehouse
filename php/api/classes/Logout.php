<?php

/**
 * Class for api logout request.
 * @author H. Eilers
 *
 */
class Logout extends Request{
	protected $sessionId = "";
	
	/**
	 * Constructor
	 * @param integer $session	Session ID
	 */
	public function __construct($session){
		parent::__construct( "logout" );
		$this->sessionId = $session;
	}
	
	/**
	 * Destroys session
	 * @return true if session destroyed, false otherwise.
	 */
	public function logout(){
		$session = new Session( $this->sessionId );
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
				&& $object->f == 'logout'
				&& property_exists($object, 'sessionId') )
			return true;
		return false;
	}
}

?>
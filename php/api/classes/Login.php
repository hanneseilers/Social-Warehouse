<?php

/**
 * Class for api login request.
 * @author H. Eilers
 *
 */
class Login extends Request{
	protected $warehouseId = 0;
	protected $pw = "";
	
	/**
	 * Constructor
	 * @param integer $warehouse	Warehouse ID		
	 * @param string $password		MD5 password for login
	 */
	public function __construct($warehouseId, $password){
		parent::__construct( "login" );
		$this->pw = $password;
		$this->warehouseId = $warehouseId;
	}
	
	/**
	 * Tries to login.
	 * @param session id if logged in successfull, false otherwise.
	 */
	public function login(){	
		// get warehouse
		$warehouse = new Warehouse( $this->warehouseId );
		$vSession = false;
		if( $warehouse->checkPassword($this->pw) )
			$vSession = new Session( $warehouse->id, true, false );
		elseif( $warehouse->checkPasswordRestricted($this->pw) )
			$vSession = new Session( $warehouse->id, true );
		
		return $vSession;
	}
	
	/**
	 * Checks if session is still logged in
	 * @param integer $sessionId	Session ID
	 */
	public static function checkLogin($sessionId){
		$session = new Session( $sessionId );
		if( $session->sessionId > 0 )
			return true;
		return false;
	}
	
	/**
	 * Checks if object is instance of Login class.
	 * @param mixed $object	Object to check
	 * @return true if is Login class object, false otherwise.
	 */
	public static function isInstance($object){
		if( parent::isInstance($object)
				&& property_exists($object, 'warehouseId')
				&& property_exists($object, 'pw') )
			return true;
		return false;
	}	
	
}

?>
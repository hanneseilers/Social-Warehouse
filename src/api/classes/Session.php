<?php

/**
 * Session class
 * @author H. Eilers
 *
 */
class Session{
	
	public $sessionId = -1;
	public $warehouseId = -1;
	public $restricted = true;
	
	/**
	 * Constructor
	 * Always validates database sessions first.
	 * @param integer $id			Warehouse ID or session ID
	 * @param string $create		Set true to create new session (@param $id is warehouse ID),
	 * 								Set false to create existing session (@param $id is session ID).
	 * @param string $restricted	Set true to create restricted session, false otherwise.
	 */
	public function __construct($id=null, $create=false, $restricted=true){
		// validate sessions
		//Session::validateSessions();
		
		// create new session or update from database
		if( $create )
			$this->create( $id, $restricted );
		else
			$this->updateFromDatabase($id);
	}
	
	/**
	 * Destroys a session
	 * @return	True if session destoryed, false otherwise.
	 */
	public function destroy(){
		Session::validateSessions();
		
		$sql = "DELETE FROM ".Database::getTableName('sessions')." WHERE id=?";
		$response = Database::getInstance()->sql( 'deleteSession', $sql, 'i', [$this->sessionId], false );
		if( is_array($response) ){
			return true;
		}
		return false;
	}
	
	/**
	 * Updated session (keep alive).
	 */
	public function update(){
		$sql = "UPDATE ".Database::getTableName('sessions')." SET lastUpdate=NOW()";
		Database::getInstance()->sql( 'updateSession', $sql, '', [], false );
	}
	
	/**
	 * Creates a new session
	 * @param integer $warehouseId	Warehouse ID
	 * @param string $restricted	Set true to create restricted session, false otherwise.
	 */
	private function create($warehouseId, $restricted=true){
		// create a new session
		$sql = "INSERT INTO ".Database::getTableName('sessions')." (warehouse, restricted) VALUES(?, ?)";
		$result = Database::getInstance()->sql( 'insertSession', $sql, 'ii', [$warehouseId, $restricted], false );
		if( $result && is_int($result) && $result > 0 ){
			$this->sessionId = $result;
			$this->warehouseId = $warehouseId;
			$this->restricted = $restricted;
		}
	}
	
	/**
	 * Loads session data from database.
	 * @param integer $id	Session ID
	 */
	private function updateFromDatabase($id){
		$sql = "SELECT id, warehouse, restricted FROM ".Database::getTableName('sessions')." WHERE id=?";
		$result = Database::getInstance()->sql( 'getSession', $sql, 'i', [$id], false );
		if( is_array($result) && count($result) > 0 ){
			$this->sessionId = $result[0]['id'];
			$this->warehouseId = $result[0]['warehouse'];
			$this->restricted = ($result[0]['restricted'] == 1 ? true : false);
		}
	}
	
	/**
	 * Validates sessions in database.
	 * All sessions that are older than Config::$sessionTimeout get deleted.
	 */
	private static function validateSessions(){
		$sql = "DELETE FROM ".Database::getTableName('sessions')." WHERE lastUpdate < DATE_ADD( NOW(), INTERVAL ? SECOND )";
		Database::getInstance()->sql( 'validateSessions', $sql, 'i', [-1*Config::$sessionTimeout], false );
	}
	
	public static function getActiveSessionsNumber(){
		Session::validateSessions();
		$sql = "SELECT COUNT(id) AS activeSessions FROM ".Database::getTableName('sessions');
		$response = Database::getInstance()->sql( 'getActiveSessions', $sql, '', [], false );
		if( is_array($response) && count($response) > 0 )
			return $response[0]['activeSessions'];
		return 0;
	}
	
}

?>
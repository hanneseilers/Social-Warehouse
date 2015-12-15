<?php

/**
 * Warehouse class
 * @author H. Eilers
 *
 */
class Warehouse{
	
	public $id = 0;
	public $name = null;
	public $description = null;
	public $country = null;
	public $city = null;
	
	private $password = null;
	private $passwordRestricted = null;
	private $mail = null;
	private $disableLocationLess = false;
	private $disablePaletteLess = false;
	
	/**
	 * Constructor
	 * @param integer $id			ID of warehouse
	 * @param boolean $update	Set true to force update from database,
	 * 								false (default) uses cached data.
	 */
	public function __construct($id=null, $update=false){
		if( $id == null )
			$this->create();
		elseif( is_integer($id) && $id > 0 )
			$this->updateFromDatabase( $id, $update );			
	}
	
	/**
	 * Checks if password fits warehouse password.
	 * @param string $password	Normal access password
	 */
	public function checkPassword($password){
		return ($password && $this->password === $password);
	}
	
	/**
	 * Checks if password fits restricted password
	 * @param string $password	Restricted access password
	 */
	public function checkPasswordRestricted($password){
		return ($password && $this->passwordRestricted === $password);
	}
	
	/**
	 * @return Warehouse mail address
	 */
	public function getMail(){
		return $this->mail;
	}
	
	/**
	 * @return true if stock without location is disabled, false otherwise.
	 */
	public function isLocationLessDisabled(){
		return $this->disableLocationLess;
	}
	
	/**
	 * @return true if stock withoput palette is disabled, false otherwise.
	 */
	public function isPaletteLessDisabled(){
		return $this->disablePaletteLess;
	}
	
	/**
	 * Set mail address
	 * @param string $mail
	 */
	public function setMail($mail){
		if( is_string($mail) && strlen($mail) > 5 )
			$this->mail = $mail;
	}
	
	/**
	 * Sets if stock without location is disabled.
	 * @param boolean $disable	Set true to disable stock without location, false otherwise.
	 */
	public function setDisableLocationLess($disable){
		$this->disableLocationLess = $disable;
	}
	
	/**
	 * Sets if stock without palette is disabled.
	 * @param boolean $disable	Set true to disable stock without palette, false otherwise
	 */
	public function setDisablePaletteLess($disable){
		$this->disablePaletteLess = $disable;
	}
	
	/**
	 * Sets normal access password
	 * @param string $password
	 */
	public function setPassword($password){
		if( is_string($password) && strlen($password) > 0 )
			$this->password = $password;
	}
	
	/**
	 * Sets restricted access password
	 * @param string $password
	 */
	public function setPasswordRestricted($password){
		if( is_string($password) && strlen($password) > 0 )
			$this->passwordResticted = $password;
	}
	
	/**
	 * Updates warehouse data in database.
	 */
	public function edit(){
		if( $this->id > 0
				&& is_string($this->name) && strlen($this->name) > 0
				&& is_string($this->password) && strlen($this->password) > 0 ){
			$sql = "UPDATE ".Database::getTableName('warehouses')
				." SET name=?, description=?, country=?, city=?, mail=?, password=?, passwordRestricted=?, disableLocationLess=?, disablePaletteLess=? WHERE id=?";
			$response = Database::getInstance()->sql( 'editWarehouse', $sql, 'sssssssiii', [
					$this->name,
					$this->description,
					$this->country,
					$this->city,
					$this->mail,
					$this->password,
					$this->passwordRestricted,
					$this->disableLocationLess,
					$this->disablePaletteLess,
					$this->id
			], false );
			
			if( is_array($response) ){
				Log::debug( 'Edited warehouse ID #'.$this->id );
				return true;
			}
		} else {
			Log::warn( 'Warehouse id is #'.$this->id );
		}
		
		return false;
	}
	
	/**
	 * Deletes warehouse from database.
	 */
	public function delete(){		
		// delete warehouse
		$sql = "DELETE FROM ".Database::getTableName('warehouses')." WHERE id=?";
		$response = Database::getInstance()->sql( 'deleteWarehouse', $sql, 'i', [$this->id], false );
		return is_array( $response );
	}
	
	private function create(){
		$sql = "INSERT INTO ".Database::getTableName('warehouses')." (name) VALUES ('')";
		$id = Database::getInstance()->sql( 'insertWarehouse', $sql, '', [], false );
		if( is_integer($id) ){
			$this->id = $id;
			Log::debug( 'Created warehouse #'.$id );
		}
	}
	
	/**
	 * Updates warehosedata from database
	 * @param integer $id		ID of warehouse
	 * @param boolean $update	Set true to force update from database,
	 * 							false (default) uses cached data.
	 */
	private function updateFromDatabase($id, $update=false){
		$sql = "SELECT * FROM ".Database::getTableName('warehouses')." WHERE id=?";
		$result = Database::getInstance()->sql( 'getWarehouse', $sql, 'i', [$id], !$update );
		
		if( $result && count($result) > 0 ){
			$result = $result[0];
			$this->id = $result['id'];
			$this->name = $result['name'];
			$this->description = $result['description'];
			$this->country = $result['country'];
			$this->city = $result['city'];
			$this->password = $result['password'];
			$this->passwordRestricted = $result['passwordRestricted'];
			$this->mail = $result['mail'];
			$this->disableLocationLess = $result['disableLocationLess'];
			$this->disablePaletteLess = $result['disablePaletteLess'];
		}
	}
	
	/**
	 * @return array of warehouses.
	 */
	public static function getWarehouses(){
		$warehouses = array();
		$sql = "SELECT * FROM ".Database::getTableName('warehouses')." ORDER BY country ASC, city ASC, name ASC";
		$response = Database::getInstance()->sql( 'getWarehouses', $sql, '', [], true );
		
		if( is_array($response) ){
			foreach( $response as $entry ){
				$warehouse = new Warehouse(-1);
				$warehouse->id = $entry['id'];
				$warehouse->name = $entry['name'];
				$warehouse->country = $entry['country'];
				$warehouse->city = $entry['city'];
				$warehouse->setPassword( $entry['password'] );
				$warehouse->setPasswordRestricted( $entry['passwordRestricted'] );
				$warehouse->setMail( $entry['mail'] );
				$warehouse->setDisableLocationLess( $entry['disableLocationLess'] );
				$warehouse->setDisablePaletteLess( $entry['disablePaletteLess'] );
				array_push( $warehouses, $warehouse );
			}
		}
		
		return $warehouses;
	}
	
}

?>
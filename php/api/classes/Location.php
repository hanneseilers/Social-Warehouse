<?php

/**
 * Location class
 * @author H. Eilers
 *
 */
class Location{
	
	public $id = 0;
	public $name = "";
	public $warehouseId = 0;
	
	/**
	 * Constructor
	 * @param integer 	$id				Location ID, null to create new one, -1 for doeing nothing.
	 * @param number 	$warehouseId	Warehouse ID
	 * @param boolean 	$update			Set true to receive location information
	 * 									from database, instead of from cache. 
	 */
	public function __construct($id=null, $warehouseId=0, $update=false){
		if( $id == null )
			$this->create( $warehouseId );
		elseif( is_integer($id) && $id > 0 )
			$this->updateFromDatabase( $id, $warehouseId, $update );
	}
	
	/**
	 * Update location entry in database.
	 */
	public function edit(){
		if( $this->id > 0 && is_string($this->name) && strlen($this->name) > 0 ){
			$sql = "UPDATE ".Database::getTableName('locations')." SET name=? WHERE warehouse=? AND id=?";
			$response = Database::getInstance()->sql( 'editLocation', $sql, 'sii', [$this->name, $this->warehouseId, $this->id] );
			
			if( is_array($response) ){
				Log::debug( 'Edited location #'.$this->id );
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Deletes location
	 */
	public function delete(){
		// Delete location
		$sql = "DELETE FROM ".Database::getTableName('locations')." WHERE warehouse=? AND id=?";
		$response = Database::getInstance()->sql( 'deleteLocation', $sql, 'ii', [$this->warehouseId, $this->id], false );
		return is_array( $response );
	}
	
	/**
	 * Gets cartons from palette
	 */
	public function getCartons(){
		$cartons = array();
		$sql = "SELECT * FROM ".Database::getTableName('cartons')." WHERE warehouse=? AND location=?";
		$response = Database::getInstance()->sql( "getLocationCartons", $sql, 'ii', [$this->warehouseId, $this->id], false );
		if( is_array($response) ){
			foreach( $response as $entry ){
				$carton = new Carton( $entry['id'], $this->warehouseId, null, null, true );
				array_push( $cartons, $carton );
			}
		}
		return $cartons;
	}
	
	/**
	 * Creates a new location.
	 * @param integer $warehouseId	Warehouse ID
	 */
	private function create($warehouseId){
		$sql = "INSERT INTO ".Database::getTableName('locations')." (warehouse) VALUES(?)";
		$id = Database::getInstance()->sql( "insertLocation", $sql, 'i', [$warehouseId], false );
		$this->id = $id;
		$this->warehouseId = $warehouseId;
		Log::debug( 'Added location #'.$this->id );
	}
	
	/**
	 * Receive location from database.
	 * @param integer $id			Location ID
	 * @param integer $warehouseId	Warehouse ID
	 * @param integer $update		Set true to update from database, insteadt from cache.
	 */
	private function updateFromDatabase($id, $warehouseId, $update){
		$sql = "SELECT * FROM ".Database::getTableName('locations')." WHERE warehouse=? AND id=?";
		$response = Database::getInstance()->sql( 'getLocation', $sql, 'ii', [$warehouseId, $id], !$update );
		if( $response && count($response) > 0 ){
			$response = $response[0];
			$this->id = $id;
			$this->name = $response['name'];
			$this->warehouseId = $response['warehouse'];
		}
	}
	
	/**
	 * gets loactions of warehouse.
	 * @param integer $warehouseId	Warehouse ID
	 */
	public static function getLocations($warehouseId){
		$locations = array();
		$sql = "SELECT * FROM ".Database::getTableName('locations')." WHERE warehouse=? ORDER BY name";
		$response = Database::getInstance()->sql( 'getLocations', $sql, 'i', [$warehouseId] );
		
		if( is_array($response) ){
			foreach( $response as $entry ){
				$location = new Location(-1);
				$location->id = $entry['id'];
				$location->warehouseId = $entry['warehouse'];
				$location->name = $entry['name'];
				array_push( $locations, $location );
			}
		}
		
		return $locations;
	}
	
}
?>
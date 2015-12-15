<?php

/**
 * Carton class
 * @author H. Eilers
 *
 */
class Carton{
	
	public $id = 0;
	public $warehouseId = 0;
	public $locationId = 0;
	public $paletteId = 0;
	
	/**
	 * Constructor
	 * @param integer $id			Carton ID, null to create new one, -1 for doeing nothing.
	 * @param integer $warehouseId	Warehouse ID, if to create new carton.
	 * @param integer $locationId	Location ID, if to create new carton.
	 * @param integer $paletteId	Palette ID, if to create new carton.
	 * @param boolean $update		Set true to receive carton information
	 * 								from database, instead of from cache.
	 */
	public function __construct($id=null, $warehouseId=0, $locationId=null, $paletteId=null, $update=false){
		if( $id == null ){
			$this->create($warehouseId, $locationId, $paletteId);
		} elseif( is_integer($id) && $id > 0 ){
			$this->updateFromDatabase($id, $warehouseId, $update);
		}
	}
	
	/**
	 * Update carton entry in database
	 */
	public function edit(){
		if( $this->id > 0 && $this->warehouseId > 0 ){
			if( $this->paletteId != null && $this->paletteId > 0 ){
				$palette = new Palette( $this->paletteId, $this->warehouseId );
				if( $palette )
					$this->locationId = $palette->locationId;
			}
			
			$sql = "UPDATE ".Database::getTableName('cartons')." SET warehouse=?, location=?, palette=? WHERE id=?";
			$response = Database::getInstance()->sql( 'editCarton', $sql, 'iiii', [
					$this->warehouseId,
					$this->locationId,
					$this->paletteId,
					$this->id
			], false );
			if( is_array($response) ){
				Log::debug( 'Edited carton #'.$this->id );
				return true;
			} else {
				Log::warn( 'carton id is #'.$this->id );
			}
		}
		
		return false;
	}
	
	/**
	 * Deletes carton
	 */
	public function delete(){
		$sql = "UPDATE ".Database::getTableName('stock')." SET outgo=income WHERE carton=?";
		return Database::getInstance()->sql( 'deleteCarton', $sql, 'i', [$this->id], false );
	}
	
	/**
	 * Creates a new carton
	 * @param integer $warehouseId	Warehouse ID
	 * @param integer $locationId	Location ID
	 * @param integer $paletteId	Palette ID
	 */
	private function create($warehouseId, $locationId, $paletteId){
		$sql = "INSERT INTO ".Database::getTableName('cartons')." (warehouse, location, palette) VALUES(?, ?, ?)";
		$id = Database::getInstance()->sql( 'insertCarton', $sql, 'iii', [
			$warehouseId,
			$locationId,
			$paletteId
		] );
		
		if( is_integer($id) ){
			$this->id = $id;
			$this->warehouseId = $warehouseId;
			$this->locationId = $locationId;
			$this->paletteId = $paletteId;
			Log::debug( 'Created new carton #'.$id );
		}
	}
	
	/**
	 * Receive carton from database.
	 * @param integer $id			Carton ID
	 * @param integer $warehouseId	Warehouse ID
	 * @param integer $update		Set true to update from database, insteadt from cache.
	 */
	private function updateFromDatabase($id, $warehouseId, $update){
		$sql = "SELECT * FROM ".Database::getTableName('cartons')." WHERE warehouse=? AND id=?";
		$response = Database::getInstance()->sql( 'getCarton', $sql, 'ii', [$warehouseId, $id], !$update );
		if( $response && count($response)>0 ){
			$response = $response[0];
			$this->id = $response['id'];
			$this->warehouseId = $response['warehouse'];
			$this->locationId = $response['location'];
			$this->paletteId = $response['palette'];
		}
	}
	
}

?>
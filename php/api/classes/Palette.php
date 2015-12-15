<?php

/**
 * Palette class
 * @author H. Eilers
 *
 */
class Palette{
	
	public $id = 0;
	public $warehouseId = 0;
	public $locationId = null;
	
	/**
	 * Constructor
	 * @param integer 	$id				Palette ID, null to create new one, -1 for doeing nothing.
	 * @param number 	$warehouseId	Warehouse ID
	 * @param boolean 	$update			Set true to receive Palette information
	 * 									from database, instead of from cache.
	 */
	public function __construct($id=null, $warehouseId=0, $update=false){
		if( $id == null )
			$this->create( $warehouseId );
		elseif( is_integer($id) && $id > 0 )
			$this->updateFromDatabase( $id, $warehouseId, $update );
	}
	
	/**
	 * Update palette entry in database.
	 */
	public function edit(){
		if( $this->id > 0 && $this->warehouseId > 0 ){
			$sql = "UPDATE ".Database::getTableName('palettes')." SET location=? WHERE warehouse=? AND id=?";
			$response = Database::getInstance()->sql( 'editPalette', $sql, 'iii', [$this->locationId, $this->warehouseId, $this->id] );
				
			if( is_array($response) ){
				Log::debug( 'Edited palette #'.$this->id );
				return true;
			}
		}
	
		return false;
	}
	
	/**
	 * Deletes palette
	 */
	function delete(){
		// Delete palette
		$sql = "DELETE FROM ".Database::getTableName('palettes')." WHERE warehouse=? AND id=?";
		$response = Database::getInstance()->sql( 'deletePalette', $sql, 'ii', [$this->warehouseId, $this->id] );
		return is_array( $response );
	}
	
	/**
	 * Creates a new palette.
	 * @param integer $warehouseId	Warehouse ID
	 */
	private function create($warehouseId){
		$sql = "INSERT INTO ".Database::getTableName('palettes')." (warehouse) VALUES(?)";
		$id = Database::getInstance()->sql( "insertPalette", $sql, 'i', [$warehouseId], false );
		$this->id = $id;
		$this->warehouseId = $warehouseId;
		Log::debug( 'Added palette #'.$this->id );
	}
	
	/**
	 * Receive palette from database.
	 * @param integer $id			Palette ID
	 * @param integer $warehouseId	Warehouse ID
	 * @param integer $update		Set true to update from database, insteadt from cache.
	 */
	private function updateFromDatabase($id, $warehouseId, $update){
		$sql = "SELECT * FROM ".Database::getTableName('palettes')." WHERE warehouse=? AND id=?";
		$response = Database::getInstance()->sql( 'getPalette', $sql, 'ii', [$warehouseId, $id], !$update );
		
		if( $response && count($response) > 0 ){
			$response = $response[0];
			$this->id = $id;
			$this->warehouseId = $response['warehouse'];
			$this->locationId = $response['location'];
		}
	}
	
	/**
	 * Get all paletets of a warehouse.
	 * @param integer $warehouseId	Warehouse ID
	 */
	public static function getPalettes($warehouseId){
		$palettes = array();
		$sql = "SELECT * FROM ".Database::getTableName('palettes')." WHERE warehouse=?";
		$response = Database::getInstance()->sql( 'getPalettes', $sql, 'i', [$warehouseId], false );
		
		if( is_array($response) ){
			foreach( $response as $entry ){
				$palette = new Palette(-1);
				$palette->id = $entry['id'];
				$palette->warehouseId = $entry['warehouse'];
				$palette->locationId = $entry['location'];
				array_push( $palettes, $palette );
			}
		}
		return $palettes;
	}
	
}

?>
<?php

/**
 * Palette class
 * @author H. Eilers
 *
 */
class Palette{
	
	public $id = 0;
	public $number = 0;
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
				
				// update cartons location
				$sql = "UPDATE ".Database::getTableName('cartons')." SET location=? WHERE palette=?";
				$response = Database::getInstance()->sql( 'editCartonLocation', $sql, 'ii', [$this->locationId, $this->id] );
				
				Log::debug( 'Edited palette #'.$this->id );
				return true;
			}
		}
	
		return false;
	}
	
	/**
	 * Deletes palette
	 */
	public function delete(){
		// Delete palette
		$sql = "UPDATE ".Database::getTableName('stock')." JOIN ".Database::getTableName('cartons')
			." ON ".Database::getTableName('stock').".carton=".Database::getTableName('cartons').".id"
			." SET outgo=income"
			." WHERE ".Database::getTableName('cartons').".palette=?";
		$response = Database::getInstance()->sql( 'deletePalette', $sql, 'i', [$this->id] );
		return is_array( $response );
	}
	
	/**
	 * Gets cartons from palette
	 */
	public function getCartons(){
		$cartons = array();
		$sql = "SELECT * FROM ".Database::getTableName('cartons')." WHERE warehouse=? AND palette=?";
		$response = Database::getInstance()->sql( "getPaletteCartons", $sql, 'ii', [$this->warehouseId, $this->id], false );
		if( is_array($response) ){
			foreach( $response as $entry ){
				$carton = new Carton( $entry['id'], $this->warehouseId, null, null, true );
				array_push( $cartons, $carton );
			}
		}
		return $cartons;
	}
	
	/**
	 * Creates a new palette.
	 * @param integer $warehouseId	Warehouse ID
	 */
	private function create($warehouseId){
		$number = Palette::getMaxNumber($warehouseId) + 1;
		$sql = "INSERT INTO ".Database::getTableName('palettes')." (number, warehouse) VALUES(?, ?)";
		$id = Database::getInstance()->sql( "insertPalette", $sql, 'ii', [$number, $warehouseId], false );
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
			$this->number = $response['number'];
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
		$sql = "SELECT * FROM ".Database::getTableName('palettes')." WHERE warehouse=? ORDER BY number DESC";
		$response = Database::getInstance()->sql( 'getPalettes', $sql, 'i', [$warehouseId], false );
		
		if( is_array($response) ){
			foreach( $response as $entry ){
				$palette = new Palette(-1);
				$palette->id = $entry['id'];
				$palette->number = $entry['number'];
				$palette->warehouseId = $entry['warehouse'];
				$palette->locationId = $entry['location'];
				array_push( $palettes, $palette );
			}
		}
		return $palettes;
	}
	
	/**
	 * Gets latest palette number.
	 * @param integer $warehouseId	Warehouse ID
	 */
	public static function getMaxNumber($warehouseId){
		$sql = "SELECT MAX(number) AS max FROM ".Database::getTableName('palettes')." WHERE warehouse=?";
		$response = Database::getInstance()->sql( 'getPalettes', $sql, 'i', [$warehouseId], false );
		if( is_array($response) && count($response) > 0 ){
			return $response[0]['max'];
		}
		
		return 0;
	}
	
}

?>
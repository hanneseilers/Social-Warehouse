<?php

	function db_getPalettes($warehouseId){
		$sql = "SELECT * FROM palettes WHERE warehouse=".$warehouseId." ORDER BY name ASC";
		$palettes = dbSQL($sql);
		
		// add locations
		if( $palettes ){
			for( $i=0; $i < count($palettes); $i++ ){
				$storages = db_getPaletteStorages( $palettes[$i]['id'] );
				if( count($storages) > 0 ){
					$palettes[$i]['location'] = $storages[0]['location'];
				} else {
					$palettes[$i]['location'] = null;
				}
			}
		}
		
		return $palettes;
	}
	
	function db_addPalette($warehouseId, $name){
		$sql = "SELECT * FROM palettes WHERE warehouse=".$warehouseId." AND name='".$name."'";
		if( count(dbSQL($sql)) == 0 ){
			$sql = "INSERT INTO palettes (warehouse, name) VALUES (".$warehouseId.", '".$name."')";
			return dbSQL($sql);
		}
		
		return false;
	}
	
	function db_deletePalette($warehouseId, $id){
		$sql = "SELECT * FROM palettes WHERE warehouse=".$warehouseId." AND id=".$id;
		if( count(dbSQL($sql)) > 0 ){
			// delete storage data
			$sql = "DELETE FROM storages WHERE palette=".$id;
			dbSQL($sql);
			
			// delete palette
			$sql = "DELETE FROM palettes WHERE warehouse=".$warehouseId." AND id=".$id;
			return dbSQL($sql);
		}
		
		return false;
	}
	
	function db_editPalette($warehouseId, $id, $name){
		$sql = "SELECT * FROM palettes WHERE warehouse=".$warehouseId." AND name='".$name."'";
		if( count(dbSQL($sql)) == 0 ){
			$sql = "UPDATE palettes SET name='".$name."' WHERE warehouse=".$warehouseId." AND id=".$id;
			$return = dbSQL($sql);
			
			db_validatePaletteLocations();
			return $return;
		}
		
		return false;
	}
	
	function db_getPaletteStorages($palette){
		$sql = "SELECT * FROM storages WHERE palette=".$palette." AND location IS NOT NULL";
		return dbSQL($sql);
	}
	
	function db_validatePaletteLocations(){
		$sql = "SELECT * FROM palettes";
		$palettes = dbSQL($sql);
		
		// check every palette
		foreach( $palettes as $palette ){
			
			// get location
			$storages = db_getPaletteStorages( $palette['id'] );
			
			// update locations
			if( count($storages) > 0 ){
				$location = $storages[0]['location'];
				$sql = "UPDATE storages SET location=".$location." WHERE palette=".$palette['id'];
				dbSQL($sql);
			}
			
		}
	}
?>
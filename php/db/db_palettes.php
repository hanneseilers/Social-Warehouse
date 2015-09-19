<?php

	function db_getPalettes($warehouseId){
		$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."palettes WHERE warehouse=".$warehouseId." ORDER BY name DESC";
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
		$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."palettes WHERE warehouse=".$warehouseId." AND name='".$name."'";
		if( count(dbSQL($sql)) == 0 ){
			
			$sql = "INSERT INTO ".$GLOBALS['dbPrefix']."palettes (warehouse, name) VALUES (".$warehouseId.", '".$name."')";
			if( dbSQL($sql) ){
				
				// get new id
				$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."palettes WHERE warehouse=".$warehouseId." AND name='".$name."'";
				$result = dbSQL($sql);
				if( $result ){
					return $result[0]['id'];
				}
			}
			
		}
		
		return -1;;
	}
	
	function db_deletePalette($warehouseId, $id){
		$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."palettes WHERE warehouse=".$warehouseId." AND id=".$id;
		if( count(dbSQL($sql)) > 0 ){
			// delete storage data
			$sql = "DELETE FROM ".$GLOBALS['dbPrefix']."storages WHERE palette=".$id;
			dbSQL($sql);
			
			// delete palette
			$sql = "DELETE FROM ".$GLOBALS['dbPrefix']."palettes WHERE warehouse=".$warehouseId." AND id=".$id;
			return dbSQL($sql);
		}
		
		return false;
	}
	
	function db_clearPalette($warehouseId, $id){
		$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."palettes WHERE warehouse=".$warehouseId." AND id=".$id;
		if( count(dbSQL($sql)) > 0 ){				
			// mark palette as cleared
			$sql = "UPDATE ".$GLOBALS['dbPrefix']."palettes SET cleared=1 WHERE id=".$id;
			dbSQL($sql);
			
			// reset storage incom
			$sql = "UPDATE ".$GLOBALS['dbPrefix']."storages SET location=NULL, income=0 WHERE palette=".$id;
			return dbSQL($sql);
		}
	
		return false;
	}
	
	function db_editPalette($warehouseId, $id, $name){
		$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."palettes WHERE warehouse=".$warehouseId." AND name='".$name."'";
		if( count(dbSQL($sql)) == 0 ){
			$sql = "UPDATE ".$GLOBALS['dbPrefix']."palettes SET name='".$name."' WHERE warehouse=".$warehouseId." AND id=".$id;
			$return = dbSQL($sql);
			
			db_validatePaletteLocations();
			return $return;
		}
		
		return false;
	}
	
	function db_movePalette($palette, $location){
		$sql = "UPDATE ".$GLOBALS['dbPrefix']."storages SET location=".($location == "NULL" ? "NULL" : $location)." WHERE palette=".$palette;
		$return = dbSQL($sql);
		print $sql;
		db_validatePaletteLocations();
		return $return;
	}
	
	function db_getPaletteStorages($palette){
		$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."storages WHERE palette=".$palette." AND location IS NOT NULL";
		return dbSQL($sql);
	}
	
	function db_validatePaletteLocations(){
		$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."palettes";
		$palettes = dbSQL($sql);
		
		// check every palette
		foreach( $palettes as $palette ){
			
			// get location
			$storages = db_getPaletteStorages( $palette['id'] );
			
			// update locations
			if( count($storages) > 0 ){
				$location = $storages[0]['location'];
				$sql = "UPDATE ".$GLOBALS['dbPrefix']."storages SET location=".$location." WHERE palette=".$palette['id'];
				dbSQL($sql);
			}
			
		}
	}
?>
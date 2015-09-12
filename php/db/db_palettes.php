<?php

	function db_getPalettes($warehouseId){
		$sql = "SELECT * FROM palettes WHERE warehouse=".$warehouseId." ORDER BY name ASC";
		return dbSQL($sql);
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
		$sql = "DELETE FROM palettes WHERE warehouse=".$warehouseId." AND id=".$id;
		return dbSQL($sql);
	}
	
	function db_editPalette($warehouseId, $id, $name){
		$sql = "UPDATE palette SET name='".$name."' WHERE warehouse=".$warehouseId." AND id=".$id;
		return dbSQL($sql);
	}
?>
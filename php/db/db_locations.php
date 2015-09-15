<?php
	
	function db_getLocations($warehouseId){
		$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."locations WHERE warehouse=".$warehouseId;
		return dbSQL($sql);
	}
	
	function db_editLocation($id, $name){
		$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."locations WHERE name='".$name."'";
		if( count(dbSQL($sql)) == 0 ){
			$sql = "UPDATE ".$GLOBALS['dbPrefix']."locations SET name='".$name."' WHERE id=".$id;
			return dbSQL($sql);
		}
		
		return false;
	}
	
	function db_addLocation($warehouseId, $name){
		$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."locations WHERE name='".$name."'";
		if( count(dbSQL($sql)) == 0 ){
			$sql = "INSERT INTO ".$GLOBALS['dbPrefix']."locations (warehouse, name) VALUES (".$warehouseId." ,'".$name."')";
			return dbSQL($sql);
		}
		
		return false;
	}
	
	function db_deleteLocation($warehouseId, $id){
		$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."location WHERE warehouse=".$warehouseId." AND id=".$id;
		if( count(dbSQL($sql)) > 0 ){
			// delete storages
			$sql = "DELETE FROM ".$GLOBALS['dbPrefix']."storages WHERE location=".$id;
			dbSQL($sql);
			
			// delete location
			$sql = "DELETE FROM ".$GLOBALS['dbPrefix']."locations WHERE warehouse=".$warehouseId." AND id=".$id;
			return dbSQL($sql);
		}
		
		return false;
	}

?>
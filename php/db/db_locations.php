<?php
	
	function db_getLocations($warehouseId){
		$sql = "SELECT id, name FROM ".$GLOBALS['dbPrefix']."locations WHERE warehouse=".$warehouseId." ORDER BY name ASC";
		return dbSQL($sql);
	}
	
	function db_editLocation($id, $name){
		$sql = "SELECT COUNT(id) AS num FROM ".$GLOBALS['dbPrefix']."locations WHERE name='".$name."'";
		if( dbSQL($sql)[0]['num'] == 0 ){
			$sql = "UPDATE ".$GLOBALS['dbPrefix']."locations SET name='".$name."' WHERE id=".$id;
			return dbSQL($sql);
		}
		
		return false;
	}
	
	function db_addLocation($warehouseId, $name){
		$sql = "SELECT COUNT(id) AS num FROM ".$GLOBALS['dbPrefix']."locations WHERE name='".$name."'";
		if( dbSQL($sql)[0]['num'] == 0 ){
			$sql = "INSERT INTO ".$GLOBALS['dbPrefix']."locations (warehouse, name) VALUES (".$warehouseId." ,'".$name."')";
			return dbSQL($sql);
		}
		
		return false;
	}
	
	function db_deleteLocation($warehouseId, $id){
		$sql = "SELECT COUNT(id) AS num FROM ".$GLOBALS['dbPrefix']."locations WHERE warehouse=".$warehouseId." AND id=".$id;
		if( dbSQL($sql)[0]['num'] > 0 ){
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
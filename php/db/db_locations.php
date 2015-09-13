<?php
	
	function db_getLocations($warehouseId){
		$sql = "SELECT * FROM locations WHERE warehouse=".$warehouseId;
		return dbSQL($sql);
	}
	
	function db_editLocation($id, $name){
		$sql = "SELECT * FROM locations WHERE name='".$name."'";
		if( count(dbSQL($sql)) == 0 ){
			$sql = "UPDATE locations SET name='".$name."' WHERE id=".$id;
			return dbSQL($sql);
		}
		
		return false;
	}
	
	function db_addLocation($warehouseId, $name){
		$sql = "SELECT * FROM locations WHERE name='".$name."'";
		if( count(dbSQL($sql)) == 0 ){
			$sql = "INSERT INTO locations (warehouse, name) VALUES (".$warehouseId." ,'".$name."')";
			return dbSQL($sql);
		}
		
		return false;
	}
	
	function db_deleteLocation($warehouseId, $id){
		$sql = "DELETE FROM locations WHERE warehouse=".$warehouseId." AND id=".$id;
		print $sql;
		return dbSQL($sql);
	}

?>
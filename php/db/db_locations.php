<?php
	
	function db_getLocations($warehouseId){
		$sql = "SELECT * FROM locations WHERE warehouse=".$warehouseId;
		return dbSQL($sql);
	}

?>
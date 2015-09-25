<?php

	function db_addCateory($warehouseId, $name, $parent="NULL"){		
		$sql = "SELECT COUNT(id) AS num FROM ".$GLOBALS['dbPrefix']."categories WHERE warehouse=".$warehouseId." AND name='".$name."' AND parent".($parent == "NULL" ? " IS NULL" : "=".$parent);
		if( dbSQL($sql)[0]['num'] == 0 ){
			$sql = "INSERT INTO ".$GLOBALS['dbPrefix']."categories (parent, warehouse, name, required) VALUES (".$parent.", ".$warehouseId.", '".$name."', 0)";
			return dbSQL($sql);
		}
		
		return false;		
	}
	
	function db_getCategory($warehouseId, $id){
		$sql = "SELECT id, name, parent, required FROM ".$GLOBALS['dbPrefix']."categories WHERE warehouse=".$warehouseId." AND id=".$id;
		return dbSQL($sql);
	}
	
	function db_hasChildCategory($warehouseId, $id){
		$sql = "SELECT COUNT(id) AS num FROM ".$GLOBALS['dbPrefix']."categories WHERE warehouse=".$warehouseId." AND parent=".$id;
		if( dbSQL($sql)[0]['num'] > 0 )
			return true;
		
		return false;
	}
	
	function db_deleteCategory($warehouseId, $id){
		
		$sql = "SELECT catR.id FROM ".$GLOBALS['dbPrefix']."categories AS catL JOIN ".$GLOBALS['dbPrefix']."categories AS catR ON catR.parent=catL.id WHERE catL.id=".$id." AND catL.warehouse=".$warehouseId;
		$childs = dbSQL($sql);
		
		// try to delete childs
		foreach( $childs as $child ){
			
			// delete storages
			$sql = "DELETE FROM ".$GLOBALS['dbPrefix']."storages WHERE category=".$child['id'];
			dbSQL($sql);
			
			// delete category
			$sql = "DELETE FROM ".$GLOBALS['dbPrefix']."categories WHERE id=".$child['id']." AND warehouse=".$warehouseId;
			dbSQL($sql);
		}
		
		// delete storages
		$sql = "DELETE FROM ".$GLOBALS['dbPrefix']."storages WHERE category=".$id;
		dbSQL($sql);
		
		// try to delete parent
		$sql = "DELETE FROM ".$GLOBALS['dbPrefix']."categories WHERE id=".$id;
 		return dbSQL($sql);
	}

	function db_getCategories($warehouseId, $location, $palette){
		$sql = "SELECT id, name, parent, required FROM ".$GLOBALS['dbPrefix']."categories WHERE warehouse=".$warehouseId. " ORDER BY name ASC";
		$result = dbSQL($sql);
		
		// add information about current stock
		for( $i=0; $i < count($result); $i++ ){
			$stockinfo = db_getStockInfo( $result[$i]['id'], $location, $palette );
			$result[$i]['stockinfo'] = $stockinfo;
		}
		
		return $result;
	}
	
	function db_getSubCategories($warehouseId, $parent){
		$sql = "SELECT id FROM ".$GLOBALS['dbPrefix']."categories WHERE warehouse=".$warehouseId. " AND parent=".$parent;
		return dbSQL($sql);		
	}
	
	function db_editCategory($id, $name, $required){
		$sql = "UPDATE ".$GLOBALS['dbPrefix']."categories SET name='".$name."', required=".$required." WHERE id=".$id;
		return dbSQL($sql);
	}

?>
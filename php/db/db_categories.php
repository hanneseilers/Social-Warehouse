<?php

	function db_addCateory($warehouseId, $name, $parent="NULL"){		
		$sql = "SELECT * FROM categories WHERE warehouse=".$warehouseId." AND name='".$name."' AND parent=".$parent;
		if( count(dbSQL($sql)) == 0 ){
			$sql = "INSERT INTO categories (parent, warehouse, name, required) VALUES (".$parent.", ".$warehouseId.", '".$name."', 0)";
			return dbSQL($sql);
		}
		
		return false;		
	}
	
	function db_deleteCategory($warehouseId, $id){
		$sql = "SELECT catR.id FROM categories AS catL JOIN categories AS catR ON catR.parent=catL.id WHERE catL.id=".$id." AND catL.warehouse=".$warehouseId;
		$childs = dbSQL($sql);
		
		// try to delete childs
		foreach( $childs as $child ){
			$sql = "DELETE FROM categories WHERE id=".$child['id']." AND warehouse=".$warehouseId;
			dbSQL($sql);
		}
		
		// try to delete parent
		$sql = "DELETE FROM categories WHERE id=".$id;
 		return dbSQL($sql);
	}

	function db_getCategories($warehouseId, $location, $palette){
		$sql = "SELECT * FROM categories WHERE warehouse=".$warehouseId. " ORDER BY name ASC";
		$result = dbSQL($sql);
		
		// add information about current stock
		for( $i=0; $i < count($result); $i++ ){
			$stockinfo = db_getStockInfo( $result[$i]['id'], $location, $palette );
			$result[$i]['stockinfo'] = $stockinfo[0];
		}
		
		return $result;
	}
	
	function db_editCategory($id, $name, $required){
		$sql = "UPDATE categories SET name='".$name."', required=".$required." WHERE id=".$id;
		return dbSQL($sql);
	}

?>
<?php

	function getCategoryHierarchyStrings($warehouseId, $categories){
		$hierarchies = [];
		foreach( $categories as $category ){
			array_push( $hierarchies,
						['hierarchy' => getCategoryHierarchy($warehouseId, $category['id']), 'id' => $category['id']] );
		}
		
		return $hierarchies;
	}

	function getCategoryHierarchy($warehouseId, $id){
		$hierarchy = "";
		while( $id ){
			$category = db_getCategory( $warehouseId, $id );
			if( count($category) > 0 ){
				
				if( strlen($hierarchy) != 0 )
					$hierarchy = " > ".$hierarchy;
				$hierarchy = $category[0]['name'].$hierarchy;
				$id = $category[0]['parent'];
				
			}
			
		}
			
		return $hierarchy;
	}

?>
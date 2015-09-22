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
	
	
	function getRecursiveStockInfo($warehouseId, $categoryId){
		$income = array( 'female' => 0, 'male' => 0, 'baby' => 0, 'unisex' => 0, 'asex' => 0 );
		$outgo = array( 'female' => 0, 'male' => 0, 'baby' => 0, 'unisex' => 0, 'asex' => 0 );
		$total = array( 'female' => 0, 'male' => 0, 'baby' => 0, 'unisex' => 0, 'asex' => 0 );
		$overall = 0;
		$visited = array();
		$not_visitted = array( $categoryId );
		
		while( ($id = array_pop($not_visitted)) ){
			$stock = db_getCategoryStockInfo( $id );			
			array_push( $visited, $id );

			if( gettype($stock) == "array"
					&& $stock['male'] && $stock['female'] && $stock['baby'] && $stock['unisex'] && $stock['asex'] ){
				
				// add category income and outgo
				$income['male'] += $stock['male']['income'];
				$income['female'] += $stock['female']['income'];
				$income['baby'] += $stock['baby']['income'];
				$income['unisex'] += $stock['unisex']['income'];
				$income['asex'] += $stock['asex']['income'];
				
				$outgo['male'] += $stock['male']['outgo'];
				$outgo['female'] += $stock['female']['outgo'];
				$outgo['baby'] += $stock['baby']['outgo'];
				$outgo['unisex'] += $stock['unisex']['outgo'];
				$outgo['asex'] += $stock['asex']['outgo'];
				
				$overall += $stock['overall'];
				
				// search for sub categories
				$subcategories = db_getSubCategories( $warehouseId, $id );
				foreach( $subcategories as $subcategory ){
					if( !in_array($subcategory['id'], $visited) ){
						array_push( $not_visitted, $subcategory['id'] );
					}
				}
				
			}			
		}
		
		// calculate total
		$total['male'] = $income['male'] - $outgo['male'];
		$total['female'] = $income['female'] - $outgo['female'];
		$total['baby'] = $income['baby'] - $outgo['baby'];
		$total['unisex'] = $income['unisex'] - $outgo['unisex'];
		$total['asex'] = $income['asex'] - $outgo['asex'];
		
		return array( 'income' => $income, 'outgo' => $outgo, 'total' => $total, 'overall' => $overall );
	}

?>
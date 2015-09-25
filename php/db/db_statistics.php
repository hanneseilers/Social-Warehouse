<?php

	function getCategoryHierarchyStrings($categories){
		$hierarchies = [];
		foreach( $categories as $category ){
			array_push( $hierarchies, getCategoryHierarchy($categories, $category['id']) );
		}
		
		return $hierarchies;
	}

	
	function getCategoryHierarchy($categories, $id){
		$hierarchy = [];
		$string = "";
		$level = -1;
		while( $id ){
			$category = getCategory( $categories, $id );
			if( $category ){
				// add category to hierarchy array
				array_push($hierarchy, $category);
				
				// generate string
				if( strlen($string) != 0 )
					$string = " > " . $string;
				$string = $category['name'] . $string;
				
				// set id to parent
				$id = $category['parent'];
				$level++;
			} else {
				$id = null;
			}
		}
			
		return ['string' => $string, 'hierarchy' => $hierarchy, 'level' => $level];
	}
	
	function getCategory($categories, $id){
		foreach( $categories as $category ){
			if( $category['id'] == $id ){
				return $category;
			}
		}
		
		return null;
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
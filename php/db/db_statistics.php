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
			https://www.google.de/search?client=ubuntu&channel=fs&q=ocalhost&ie=utf-8&oe=utf-8&gfe_rd=cr&ei=ZXv2VcOIEIqJ8Qek1I74Dg
		}
			
		return $hierarchy;
	}
	
	
	function getRecursiveStockInfo($warehouseId, $categoryId){
		$income = 0;
		$outgo = 0;
		$visited = [];
		$not_visitted = [$categoryId];
		while( ($id = array_pop($not_visitted)) ){
			$categorystock = db_getCategoryStockInfo( $id );
			array_push( $visited, $id );
			
			if( count($categorystock) > 0 ){
				$categorystock = $categorystock[0];				
				// add category income and outgo
				if( $categorystock['income_total'] )
					$income += $categorystock['income_total'];
				if( $categorystock['outgo_total'] )
					$outgo += $categorystock['outgo_total'];
				
				// search for sub categories
				$subcategories = db_getSubCategories( $warehouseId, $id );
				foreach( $subcategories as $subcategory ){
					if( !in_array($subcategory['id'], $visited) ){
						array_push( $not_visitted, $subcategory['id'] );
					}
				}
			}			
		}
		
		return [ 'income_total' => $income, 'outgo_total' => $outgo, 'total' => $income-$outgo ];
	}

?>
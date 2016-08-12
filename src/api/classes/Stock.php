<?php

class Stock{
	
	public static function getStock( $warehouseId, $cartonId=null, $categoryId=null, $paletteId=null, $locationId=null, $male=null, $female=null, $children=null, $baby=null, $summer=null, $winter=null, $showEmpty=false ){
		
		if( $warehouseId ){
			
			if( $cartonId ){
				return Statistic::getCartonStock($warehouseId, $cartonId);
			} else if( $paletteId ){
				return Statistic::getPaletteStock($warehouseId, $paletteId);
			} else if( $locationId ){
				return Statistic::getLocationStock($warehouseId, $locationId);
			} else if( $categoryId ){
				return Statistic::getCategoryStock($warehouseId, $categoryId);
			} else {
				return Statistic::getWarehouseStock($warehouseId);
			}
			
		}
		
	}
	
	public static function addArticle( $cartonId=0, $categoryId=0, $male=false, $female=false, $children=false, $baby=false,
			$winter=false, $summer=false, $incomeAdd=0, $outgoAdd=0 ){
		
		// check for existing stock entry
		$sql = "SELECT COUNT(carton) as count FROM ".Database::getTableName('stock')
				." WHERE carton=? AND category=? AND male=? AND female=? AND children=? AND baby=? AND winter=? AND summer=?";
		$response = Database::getInstance()->sql( 'getArticle', $sql, 'iiiiiiii', [
				$cartonId,
				$categoryId,
				$male,
				$female,
				$children,
				$baby,
				$winter,
				$summer
		], false );
		// check responsee Woche 
		if( is_array($response) && $response[0]['count'] > 0 ){
			// entry exists
			$sql = "UPDATE ".Database::getTableName('stock')." SET income=income+?, outgo=outgo+?"
					." WHERE carton=? AND category=? AND male=? AND female=? AND children=? AND baby=? AND winter=? AND summer=?";
			return Database::getInstance()->sql( 'editArticle', $sql, 'iiiiiiiiii', [
					$incomeAdd,
					($outgoAdd < 0 ? $outgoAdd*(-1) : $outgoAdd ),
					$cartonId,
					$categoryId,
					$male,
					$female,
					$children,
					$baby,
					$winter,
					$summer
			] );
		} else {
			// new entry
			$sql = "INSERT INTO ".Database::getTableName('stock')." (income, outgo, carton, category, male, female, children, baby, winter, summer)"
					." VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			return Database::getInstance()->sql( 'editArticle', $sql, 'iiiiiiiiii', [
					$incomeAdd,
					($outgoAdd < 0 ? $outgoAdd*(-1) : $outgoAdd ),
					$cartonId,
					$categoryId,
					$male,
					$female,
					$children,
					$baby,
					$winter,
					$summer
			] );
		}
				
	}
	
}

?>
<?php

class Stock{
	
	public static function getStock( $warehouseId, $cartonId=null, $categoryId=null, $paletteId=null, $locationId=null, $male=null, $female=null, $children=null, $baby=null, $summer=null, $winter=null, $showEmpty=false ){
		
		if( $warehouseId && ($cartonId || $categoryId || $paletteId || $locationId) ){
		
			$sql = "SELECT carton, category, (income-outgo) AS amount, income, outgo, male, female, children, baby, summer, winter FROM ".Database::getTableName('stock')." WHERE ";
			$where = "";
			$attributes = '';
			$data = array();
			
			// check if to show empty cartons
			if( !$showEmpty ){
				$where = Stock::addToWhere( $where, "income-outgo>?" );
				$attributes = $attributes.'i';
				array_push( $data, 0 );
			}
			
			// check if to use cartons from palette
			if( $paletteId ){
				
				// get carton ids
				$in = "(";
				$palette = new Palette( $paletteId, $warehouseId );
				$cartonIds = $palette->getCartons();
				if( count($cartonIds) > 0 ){
					foreach( $cartonIds as $carton ){
						if( strlen($in) > 1 )
							$in = $in.",?";
						else
							$in = $in."?";
						
						$attributes = $attributes.'i';
						array_push( $data, $carton->id );
					}
					$in = $in.")";
					$where = Stock::addToWhere( $where, "carton IN ".$in );
				} else {
					return false;
				}
				
			}
			
			// check if to use cartons from location
			if( $locationId ){
				// get carton ids
				$in = "(";
				$location = new Location( $locationId, $warehouseId );
				$cartonIds = $location->getCartons();
				foreach( $cartonIds as $carton ){
					if( strlen($in) > 1 )
						$in = $in.",?";
					else
						$in = $in."?";
								
					$attributes = $attributes.'i';
					array_push( $data, $carton->id );
				}
				$in = $in.")";
				if( strlen($in) <= 2 )
					return false;
				
				$where = Stock::addToWhere( $where, "carton IN ".$in );
			}
			
			// add other conditions
			if( $cartonId ){
				$where = Stock::addToWhere( $where, "carton=?" );
				$attributes = $attributes.'i';
				array_push( $data, $cartonId );
			}
			if( $categoryId ){
				$where = Stock::addToWhere( $where, "category=?" );
				$attributes = $attributes.'i';
				array_push( $data, $categoryId );
			}
			if( $male ){
				$where = Stock::addToWhere( $where, "male=?" );
				$attributes = $attributes.'i';
				array_push( $data, ($male ? 1 : 0) );
			}
			if( $female ){
				$where = Stock::addToWhere( $where, "female=?" );
				$attributes = $attributes.'i';
				array_push( $data, ($female ? 1 : 0) );
			}
			if( $children ){
				$where = Stock::addToWhere( $where, "children=?" );
				$attributes = $attributes.'i';
				array_push( $data, ($children ? 1 : 0) );
			}
			if( $baby ){
				$where = Stock::addToWhere( $where, "baby=?" );
				$attributes = $attributes.'i';
				array_push( $data, ($baby ? 1 : 0) );
			}
			if( $summer ){
				$where = Stock::addToWhere( $where, "summer=?" );
				$attributes = $attributes.'i';
				array_push( $data, ($summer ? 1 : 0) );
			}
			if( $winter ){
				$where = Stock::addToWhere( $where, "winter=?" );
				$attributes = $attributes.'i';
				array_push( $data, ($winter ? 1 : 0) );
			}
			
			$sql = $sql.$where." GROUP BY category, male, female, children, baby, summer, winter";
			$response = Database::getInstance()->sql( 'getStock'.strlen($attributes), $sql, $attributes, $data, false );
			if( count($response) > 0 )
				return $response;
			
		}
		
		return false;
		
	}
	
	private static function addToWhere($where, $add){
		if( strlen($where) > 0 )
			return $where." AND ".$add;
		return $add;
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
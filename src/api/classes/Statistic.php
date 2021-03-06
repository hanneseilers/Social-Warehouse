<?php

class Statistic{
	
	public static function getCartonStock($warehouseId, $cartonId){
		$sql = "SELECT category, male, female, children, baby, summer, winter, income, outgo FROM ".Database::getTableName('stock')
			." JOIN ".Database::getTableName('cartons')." ON ".Database::getTableName('stock').".carton=".Database::getTableName('cartons').".id"
			." WHERE ".Database::getTableName('cartons').".warehouse=? AND ".Database::getTableName('stock').".carton=?";
		$response = Database::getInstance()->sql( 'getCartonStock'.($cartonId), $sql, 'ii', [$warehouseId, $cartonId], false );
		if( count($response) > 0 )
			return $response;
		return false;
	}
	
	public static function getPaletteStock($warehouseId, $paletteId){
		$sql = "SELECT category, male, female, children, baby, summer, winter, SUM(income) AS income, SUM(outgo) AS outgo FROM ".Database::getTableName('stock')
			." JOIN ".Database::getTableName('cartons')." ON ".Database::getTableName('stock').".carton=".Database::getTableName('cartons').".id"
			." JOIN ".Database::getTableName('palettes')." ON ".Database::getTableName('cartons').".palette=".Database::getTableName('palettes').".id"
			." WHERE ".Database::getTableName('palettes').".warehouse=? AND ".Database::getTableName('palettes').".id=?"
			." GROUP BY category, male, female, children, baby, summer, winter"
			." ORDER BY category";
		$response = Database::getInstance()->sql( 'getPaletteStock'.($paletteId), $sql, 'ii', [$warehouseId, $paletteId], false );
		if( count($response) > 0 )
			return $response;
		return false;
	}
	
	public static function getLocationStock($warehouseId, $locationId){
		$sql = "SELECT category, male, female, children, baby, summer, winter, SUM(income) AS income, SUM(outgo) AS outgo FROM ".Database::getTableName('stock')
			." JOIN ".Database::getTableName('cartons')." ON ".Database::getTableName('stock').".carton=".Database::getTableName('cartons').".id"
			." WHERE ".Database::getTableName('cartons').".warehouse=? AND ".Database::getTableName('cartons').".location=?"
			." GROUP BY category, male, female, children, baby, summer, winter"
			." ORDER BY category";
		$response = Database::getInstance()->sql( 'getLocationStock'.($locationId), $sql, 'ii', [$warehouseId, $locationId], false );
		if( count($response) > 0 )
			return $response;
			return false;
	}
	
	public static function getCategoryStock($warehouseId, $categoryId){
		$sql = "SELECT number, name, category, male, female, children, baby, summer, winter, SUM(income) AS income, SUM(outgo) AS outgo FROM ".Database::getTableName('stock')
			." JOIN ".Database::getTableName('cartons')." ON ".Database::getTableName('stock').".carton=".Database::getTableName('cartons').".id"
			." LEFT JOIN ".Database::getTableName('locations')." ON ".Database::getTableName('cartons').".location=".Database::getTableName('locations').".id"
			." LEFT JOIN ".Database::getTableName('palettes')." ON ".Database::getTableName('cartons').".palette=".Database::getTableName('palettes').".id"
			." WHERE income>outgo AND ".Database::getTableName('cartons').".warehouse=? AND ".Database::getTableName('stock').".category=?"
			." GROUP BY name, number, category, male, female, children, baby, summer, winter"
			." ORDER BY name, number";
		$response = Database::getInstance()->sql( 'getCategoryStock'.($categoryId), $sql, 'ii', [$warehouseId, $categoryId], false );
		if( count($response) > 0 )
			return $response;
			return false;
	}
	
	public static function getWarehouseStock($warehouseId){
		$sql = "SELECT category, parent, ".Database::getTableName('stock').".male, ".Database::getTableName('stock').".female, ".Database::getTableName('stock').".children, ".Database::getTableName('stock').".baby, ".Database::getTableName('stock').".summer, ".Database::getTableName('stock').".winter, SUM(income) AS income, SUM(outgo) AS outgo FROM ".Database::getTableName('stock')
		." LEFT JOIN ".Database::getTableName('cartons')." ON ".Database::getTableName('stock').".carton=".Database::getTableName('cartons').".id"
		." LEFT JOIN ".Database::getTableName('categories')." ON ".Database::getTableName('stock').".category=".Database::getTableName('categories').".id"
		." WHERE ".Database::getTableName('cartons').".warehouse=?"
		." GROUP BY category, ".Database::getTableName('stock').".male, ".Database::getTableName('stock').".female, ".Database::getTableName('stock').".children, ".Database::getTableName('stock').".baby, ".Database::getTableName('stock').".summer, ".Database::getTableName('stock').".winter"
		." ORDER BY parent, category";
		$response = Database::getInstance()->sql( 'getWarehouseStock'.($warehouseId), $sql, 'i', [$warehouseId], false );
		if( count($response) > 0 )
			return $response;
			return false;
	}
	
}

?>

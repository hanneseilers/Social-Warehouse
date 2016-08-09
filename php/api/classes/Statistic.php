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
		$sql = "SELECT category, male, female, children, baby, summer, winter, income, outgo FROM ".Database::getTableName('stock')
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
	
}

?>
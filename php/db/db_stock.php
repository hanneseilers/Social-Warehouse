<?php

	function db_addToStock($category, $location, $palette, $income, $outgo, $male, $female, $baby, $estimated){
		// check if to set location to palette location
		if( $palette != "NULL" ){
			db_validatePaletteLocations();
			
			$storages = db_getPaletteStorages( $palette );
			if( count($storages) > 0 ){
				$location = $storages[0]['location'];
			}
		}
		
		// check if stock entry already available
		$sql = "SELECT * FROM storages WHERE category=".$category
			." AND location".($location == "NULL" ? " IS NULL" : "=".$location)
			." AND palette".($palette == "NULL" ? " IS NULL" : "=".$palette)
			." AND male=".$male." AND female=".$female." AND baby=".$baby." AND estimated=".$estimated;		
		$result = dbSQL($sql);
		
		// insert new
		if( count($result) == 0 ){			
			$sql = "INSERT INTO storages (category, location, palette, income, outgo, estimated, male, female, baby)"
					." VALUES (".$category.", ".$location.", ".$palette.", ".$income.", ".$outgo.", ".$estimated.", ".$male.", ".$female.", ".$baby.")";
			return dbSQL($sql);			
		} 
		
		// update existing
		$sql = "UPDATE storages SET  income=income+".$income.", outgo=outgo+".$outgo." WHERE id=".$result[0]['id'];
		$return = dbSQL($sql);
		
		db_validatePaletteLocations();
		return $return;
	}
	
	function db_getStockInfo($category, $location, $palette){
		$sql = "SELECT SUM(income) AS income_total, SUM(outgo) AS outgo_total, SUM(income)-SUM(outgo) AS total FROM storages WHERE category=".$category
			." AND location".($location == "NULL" ? " IS NULL" : "=".$location)
			." AND palette".($palette == "NULL" ? " IS NULL" : "=".$palette)."";
		return dbSQL($sql);
	}
	
	function db_getPlaetteStockInfo($palette){
		$sql = "SELECT category, SUM(income) AS income_total, SUM(outgo) AS outgo_total, SUM(income)-SUM(outgo) AS total FROM storages WHERE palette=".$palette." GROUP BY category";
		return dbSQL($sql);
	}
	
	function db_getLocationStockInfo($location){
		$sql = "SELECT category, SUM(income) AS income_total, SUM(outgo) AS outgo_total, SUM(income)-SUM(outgo) AS total FROM storages WHERE location=".$location." GROUP BY category";
		return dbSQL($sql);
	}

?>
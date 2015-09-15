<?php

	function db_addToStock($category, $location, $palette, $income, $outgo, $male, $female, $baby){
		// check if to set location to palette location
		if( $palette != "NULL" ){
			db_validatePaletteLocations();
			
			$storages = db_getPaletteStorages( $palette );
			if( count($storages) > 0 ){
				$location = $storages[0]['location'];
			}
		}
		
		// check if stock entry already available
		$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."storages WHERE category=".$category
			." AND location".($location == "NULL" ? " IS NULL" : "=".$location)
			." AND palette".($palette == "NULL" ? " IS NULL" : "=".$palette)
			." AND male=".$male." AND female=".$female." AND baby=".$baby;		
		$result = dbSQL($sql);
		
		// insert new
		if( count($result) == 0 ){			
			$sql = "INSERT INTO ".$GLOBALS['dbPrefix']."storages (category, location, palette, income, outgo, male, female, baby)"
					." VALUES (".$category.", ".$location.", ".$palette.", ".$income.", ".$outgo.", ".$male.", ".$female.", ".$baby.")";
			return dbSQL($sql);			
		} 
		
		// update existing
		$sql = "UPDATE ".$GLOBALS['dbPrefix']."storages SET  income=income+".$income.", outgo=outgo+".$outgo." WHERE id=".$result[0]['id'];
		$return = dbSQL($sql);
		
		db_validatePaletteLocations();
		return $return;
	}
	
	function db_getPalettesAtLocation( $category, $location ){
		$sql = "SELECT ".$GLOBALS['dbPrefix']."storages.income, ".$GLOBALS['dbPrefix']."storages.outgo, ".$GLOBALS['dbPrefix']."palettes.name "
				."FROM ".$GLOBALS['dbPrefix']."storages JOIN ".$GLOBALS['dbPrefix']."palettes WHERE storages.category=".$category." "
				."AND ".$GLOBALS['dbPrefix']."storages.location=".$location." AND ".$GLOBALS['dbPrefix']."storages.palette=".$GLOBALS['dbPrefix']."palettes.id";
		return dbSQL($sql);
	}
	
	function db_getStockInfo($category, $location, $palette){
		$sql = "SELECT SUM(income) AS income_total, SUM(outgo) AS outgo_total, SUM(income)-SUM(outgo) AS total "
				."FROM ".$GLOBALS['dbPrefix']."storages WHERE category=".$category
				." AND location".($location == "NULL" ? " IS NULL" : "=".$location)
				." AND palette".($palette == "NULL" ? " IS NULL" : "=".$palette)."";
		return dbSQL($sql);
	}
	
	function db_getCategoryStockInfo($category){
		$sql = "SELECT SUM(income) AS income_total, SUM(outgo) AS outgo_total, SUM(income)-SUM(outgo) AS total "
				."FROM ".$GLOBALS['dbPrefix']."storages WHERE category=".$category;
		return dbSQL($sql);
	}
	
	function db_getPlaetteStockInfo($palette){
		$sql = "SELECT category, SUM(income) AS income_total, SUM(outgo) AS outgo_total, SUM(income)-SUM(outgo) AS total "
				."FROM ".$GLOBALS['dbPrefix']."storages WHERE palette=".$palette." GROUP BY category";
		return dbSQL($sql);
	}
	
	function db_getLocationStockInfo($location){
		$sql = "SELECT category, SUM(income) AS income_total, SUM(outgo) AS outgo_total, SUM(income)-SUM(outgo) AS total "
				."FROM ".$GLOBALS['dbPrefix']."storages WHERE location=".$location." GROUP BY category";
		return dbSQL($sql);
	}

?>
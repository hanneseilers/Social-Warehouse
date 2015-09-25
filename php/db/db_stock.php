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
		$sql = "SELECT id FROM ".$GLOBALS['dbPrefix']."storages WHERE category=".$category
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
		$sql = "SELECT SUM(income), SUM(outgo), ".$GLOBALS['dbPrefix']."palettes.id, ".$GLOBALS['dbPrefix']."palettes.name "
				."FROM ".$GLOBALS['dbPrefix']."storages JOIN ".$GLOBALS['dbPrefix']."palettes WHERE ".$GLOBALS['dbPrefix']."storages.category=".$category." "
				."AND ".$GLOBALS['dbPrefix']."storages.location=".$location." AND ".$GLOBALS['dbPrefix']."storages.palette=".$GLOBALS['dbPrefix']."palettes.id";
		return dbSQL($sql);
	}
	
	function _dbGetStockInfo($category, $location, $palette, $male, $female, $baby){
		$sql = "SELECT SUM(income) AS income, SUM(outgo) AS outgo, SUM(income)-SUM(outgo) AS total "
				."FROM ".$GLOBALS['dbPrefix']."storages WHERE category=".$category
				." AND location".($location == "NULL" ? " IS NULL" : "=".$location)
				." AND palette".($palette == "NULL" ? " IS NULL" : "=".$palette)
				." AND ".($male ? "male" : "!male")
				." AND ".($female ? "female" : "!female")
				." AND ".($baby ? "baby" : "!baby");
		$result = dbSQL($sql);
		
		// check for null results
		if( gettype($result) == "array" ){
			if( $result[0]['income'] == null )
				$result[0]['income'] = 0;
			if( $result[0]['outgo'] == null )
				$result[0]['outgo'] = 0;
			if( $result[0]['total'] == null || $result[0]['total'] < 0 )
				$result[0]['total'] = 0;
				
			return $result[0];
		}
		return false;
	}
	
	function db_getStockInfo($category, $location, $palette){
		$stockMale = _dbGetStockInfo( $category, $location, $palette, true, false, false );
		$stockFemale = _dbGetStockInfo( $category, $location, $palette, false, true, false );
		$stockBaby = _dbGetStockInfo( $category, $location, $palette, false, false, true );
		$stockUnisex = _dbGetStockInfo( $category, $location, $palette, true, true, false );
		$stockAsex = _dbGetStockInfo( $category, $location, $palette, false, false, false );
		
		$overall = $stockMale['total'] + $stockFemale['total'] + $stockBaby['total'] + $stockUnisex['total'] + $stockAsex['total'];
		
		return array( 	'male' => $stockMale,
				'female' => $stockFemale,
				'baby' => $stockBaby,
				'unisex' => $stockUnisex,
				'asex' => $stockAsex,
				'overall' => $overall
		);
	}
	
	function _getCategoryGenderStock($category, $male, $female, $baby){
		$sql = "SELECT SUM(income) AS income, SUM(outgo) AS outgo, SUM(income)-SUM(outgo) AS total "
				."FROM ".$GLOBALS['dbPrefix']."storages WHERE category=".$category
				." AND ".($male ? "male" : "!male")
				." AND ".($female ? "female" : "!female")
				." AND ".($baby ? "baby" : "!baby");
		$result = dbSQL($sql);
		
		// check for null results
		if( gettype($result) == "array" ){
			if( $result[0]['income'] == null )
				$result[0]['income'] = 0;
			if( $result[0]['outgo'] == null )
				$result[0]['outgo'] = 0;
			if( $result[0]['total'] == null || $result[0]['total'] < 0 )
				$result[0]['total'] = 0;
			
			return $result[0];
		}
		return false;
	}
	
	function db_getCategoryStockInfo($category){
		$stockMale = _getCategoryGenderStock( $category, true, false, false );
		$stockFemale = _getCategoryGenderStock( $category, false, true, false );
		$stockBaby = _getCategoryGenderStock( $category, false, false, true );
		$stockUnisex = _getCategoryGenderStock( $category, true, true, false );
		$stockAsex = _getCategoryGenderStock( $category, false, false, false );
		
		$overall = $stockMale['total'] + $stockFemale['total'] + $stockBaby['total'] + $stockUnisex['total'] + $stockAsex['total'];
		
		return array( 	'male' => $stockMale,
						'female' => $stockFemale,
						'baby' => $stockBaby,
						'unisex' => $stockUnisex,
						'asex' => $stockAsex,
						'overall' => $overall
		);
	}
	
	function db_getPlaetteStockInfo($palette){
		$sql = "SELECT category, SUM(income) AS income, SUM(outgo) AS outgo, SUM(income)-SUM(outgo) AS total "
				."FROM ".$GLOBALS['dbPrefix']."storages WHERE palette=".$palette." GROUP BY category";
		return dbSQL($sql);
	}
	
	function db_getLocationStockInfo($location){
		$sql = "SELECT category, SUM(income) AS income, SUM(outgo) AS outgo, SUM(income)-SUM(outgo) AS total "
				."FROM ".$GLOBALS['dbPrefix']."storages WHERE location=".$location." GROUP BY category";
		return dbSQL($sql);
	}

?>
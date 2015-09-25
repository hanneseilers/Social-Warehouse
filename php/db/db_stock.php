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
		return dbSqlCache($sql);
	}
	
	function _dbGetStockInfo($category, $location, $palette, $male, $female, $baby){
		if( $category == "NULL" ) $category = null;
		if( $location == "NULL" ) $location = null;
		if( $palette == "NULL" ) $palette = null;
		
		$sql = "SELECT category, location, palette, SUM(income) AS income, SUM(outgo) AS outgo, SUM(income)-SUM(outgo) AS total "
				."FROM ".$GLOBALS['dbPrefix']."storages WHERE " 
				.($male ? "male" : "!male")
				." AND ".($female ? "female" : "!female")
				." AND ".($baby ? "baby" : "!baby")
				." GROUP BY category, location, palette";
		$result = dbSqlCache($sql);
		
		// check for null results
		if( gettype($result) == "array" ){
			
			// find category in result
			foreach( $result as $entry ){
				if( $entry['category'] == $category
						&& $entry['location'] == $location
						&& $entry['palette'] == $palette ){
							
					$entry['income'] = intval( $entry['income'] );
					$entry['outgo'] = intval( $entry['outgo'] );
					$entry['total'] = intval( $entry['total'] );
					if( $entry['total'] < 0 )
						$entry['total'] = 0;
						
					return $entry;
					
				}
			}
			
		}
		return array( 'income' => 0, 'outgo' => 0, 'total' => 0 );
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
		// get all gender stocks
		$sql = "SELECT category, SUM(income) AS income, SUM(outgo) AS outgo, SUM(income)-SUM(outgo) AS total "
				."FROM ".$GLOBALS['dbPrefix']."storages WHERE "
				.($male ? "male" : "!male")
				." AND ".($female ? "female" : "!female")
				." AND ".($baby ? "baby" : "!baby")
				." GROUP BY category";
		$result = dbSqlCache($sql);
		
		// check for null results
		if( gettype($result) == "array" ){
			
			// find category in result
			foreach( $result as $entry ){
				if( $entry['category'] == $category ){
							
					$entry['income'] = intval( $entry['income'] );
					$entry['outgo'] = intval( $entry['outgo'] );
					$entry['total'] = intval( $entry['total'] );
					if( $entry['total'] < 0 )
						$entry['total'] = 0;
						
					return $entry;
					
				}
			}
			
		}
		return array( 'income' => 0, 'outgo' => 0, 'total' => 0 );
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
	
	function db_getPaletteStockInfo($palette){
		$sql = "SELECT category, SUM(income) AS income, SUM(outgo) AS outgo, SUM(income)-SUM(outgo) AS total "
				."FROM ".$GLOBALS['dbPrefix']."storages WHERE palette=".$palette." GROUP BY category";
		return dbSqlCache($sql);
	}
	
	function db_getLocationStockInfo($location){
		$sql = "SELECT category, SUM(income) AS income, SUM(outgo) AS outgo, SUM(income)-SUM(outgo) AS total "
				."FROM ".$GLOBALS['dbPrefix']."storages WHERE location=".$location." GROUP BY category";
		return dbSqlCache($sql);
	}

?>
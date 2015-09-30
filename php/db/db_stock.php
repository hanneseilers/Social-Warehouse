<?php

	function db_addToStock($warehouse, $category, $location, $palette, $income, $outgo, $male, $female, $baby){
		// check if to set location to palette location
		if( $palette != "NULL" ){
			db_validatePaletteLocations();
			
			$storages = db_getPaletteStorages( $palette );
			if( count($storages) > 0 ){
				$location = $storages[0]['location'];
			}
		}
		
		// check if stock entry already available
		$sql = "SELECT id FROM ".$GLOBALS['dbPrefix']."storages WHERE warehouse=".$warehouse
			." AND category=".$category
			." AND location".($location == "NULL" ? " IS NULL" : "=".$location)
			." AND palette".($palette == "NULL" ? " IS NULL" : "=".$palette)
			." AND male=".$male." AND female=".$female." AND baby=".$baby;		
		$result = dbSQL($sql);
		
		// insert new
		if( count($result) == 0 ){			
			$sql = "INSERT INTO ".$GLOBALS['dbPrefix']."storages (warehouse, category, location, palette, income, outgo, male, female, baby)"
					." VALUES (".$warehouse.", ".$category.", ".$location.", ".$palette.", ".$income.", ".$outgo.", ".$male.", ".$female.", ".$baby.")";
			return dbSQL($sql);			
		} 
		
		// update existing
		$sql = "UPDATE ".$GLOBALS['dbPrefix']."storages SET  income=income+".$income.", outgo=outgo+".$outgo." WHERE id=".$result[0]['id'];
		$return = dbSQL($sql);
		
		db_validatePaletteLocations();
		return $return;
	}
	
	function db_getStockAtLocation( $warehouse, $category, $location ){
		$response = array();
		$palettes = array();
		$loose = db_getStockInfo( $warehouse, $category, $location, "NULL" );
		
		// get palette stocks
		$stockMale = _getPalettesGenderStockAtLocation( $warehouse, $category, $location, true, false, false );
		$stockFemale = _getPalettesGenderStockAtLocation( $warehouse, $category, $location, false, true, false );
		$stockBaby = _getPalettesGenderStockAtLocation( $warehouse, $category, $location, false, false, true );
		$stockUnisex = _getPalettesGenderStockAtLocation( $warehouse, $category, $location, true, true, false );
		$stockAsex = _getPalettesGenderStockAtLocation( $warehouse, $category, $location, false, false, false );
		
		// calculate entries
		foreach( $stockMale as $entry ){
			$palettes[$entry['id']]['name'] = $entry['name'];
			$palettes[$entry['id']]['male'] = $entry['total'];
		}
		
		foreach( $stockFemale as $entry ){
			$palettes[$entry['id']]['name'] = $entry['name'];
			$palettes[$entry['id']]['female'] = $entry['total'];
		}
		
		foreach( $stockBaby as $entry ){
			$palettes[$entry['id']]['name'] = $entry['name'];
			$palettes[$entry['id']]['baby'] = $entry['total'];
		}
		
		foreach( $stockUnisex as $entry ){
			$palettes[$entry['id']]['name'] = $entry['name'];
			$palettes[$entry['id']]['unisex'] = $entry['total'];
		}
		
		foreach( $stockAsex as $entry ){
			$palettes[$entry['id']]['name'] = $entry['name'];
			$palettes[$entry['id']]['asex'] = $entry['total'];
		}
		
		// add missing entries
		foreach( array_keys($palettes) as $key ){
			if( !isset($palettes[$key]['male']) ) $palettes[$key]['male'] = 0;
			if( !isset($palettes[$key]['female']) ) $palettes[$key]['female'] = 0;
			if( !isset($palettes[$key]['baby']) ) $palettes[$key]['baby'] = 0;
			if( !isset($palettes[$key]['unisex']) ) $palettes[$key]['unisex'] = 0;
			if( !isset($palettes[$key]['asex']) ) $palettes[$key]['asex'] = 0;
		}
		
		// add loose and palettes stock
		$response['loose'] = $loose;
		$response['palettes'] = $palettes;
		
		return $response;
	}
	
	function _getPalettesGenderStockAtLocation( $warehouse, $category, $location, $male, $female, $baby ){
		$sql = "SELECT SUM(income)-SUM(outgo) AS total, "
					.$GLOBALS['dbPrefix']."palettes.id, ".$GLOBALS['dbPrefix']."palettes.name"
				." FROM ".$GLOBALS['dbPrefix']."storages"
				." JOIN ".$GLOBALS['dbPrefix']."palettes"
					." ON ".$GLOBALS['dbPrefix']."storages.palette=".$GLOBALS['dbPrefix']."palettes.id"
				." WHERE "
					.$GLOBALS['dbPrefix']."storages.warehouse=".$warehouse
					." AND ".$GLOBALS['dbPrefix']."storages.category=".$category
					." AND ".$GLOBALS['dbPrefix']."storages.location=".$location
					." AND ".($male ? "male" : "!male")
					." AND ".($female ? "female" : "!female")
					." AND ".($baby ? "baby" : "!baby")
				." GROUP BY ".$GLOBALS['dbPrefix']."storages.palette";
		return dbSqlCache($sql);
	}
	
	function _clearStockEntry($entry){
		unset( $entry['category'] );
		unset( $entry['location'] );
		unset( $entry['palette'] );
		unset( $entry['warehouse'] );
		return $entry;
	}
	
	function _dbGetStockInfo($warehouse, $category, $location, $palette, $male, $female, $baby){
		if( $category == "NULL" ) $category = null;
		
		$sql = "SELECT category, location, palette, SUM(income) AS income, SUM(outgo) AS outgo, SUM(income)-SUM(outgo) AS total "
				."FROM ".$GLOBALS['dbPrefix']."storages WHERE "
				." warehouse=".$warehouse
				." AND ".($male ? "male" : "!male")
				." AND ".($female ? "female" : "!female")
				." AND ".($baby ? "baby" : "!baby")
				." GROUP BY category, location, palette";
		$result = dbSqlCache($sql);
		
		// check for null results
		if( gettype($result) === "array" ){
					
			// find category, location, palette in result
			foreach( $result as $entry ){
							
				// check where to search for
				if( $entry['category'] == $category ){
					
					if( $location != null && $palette != null ){
						
						// location and palette set
						$vLocation = $location;
						$vPalette = $palette;
						if( $location == "NULL" ) $vLocation = null;
						if( $palette == "NULL" ) $vPalette = null;
						
						if( $entry['location'] == $vLocation && $entry['palette'] == $vPalette ){								
							$entry['income'] = intval( $entry['income'] );
							$entry['outgo'] = intval( $entry['outgo'] );
							$entry['total'] = intval( $entry['total'] );
							if( $entry['total'] < 0 )
								$entry['total'] = 0;
						
							return _clearStockEntry($entry);							
						}					
					} else if( $location != null ){					
						// only location and palette != null
						$vLocation = $location;
						if( $location == "NULL" ) $vLocation = null;
							
						if( $entry['location'] == $vLocation && $entry['palette'] != null ){								
							$entry['income'] = intval( $entry['income'] );
							$entry['outgo'] = intval( $entry['outgo'] );
							$entry['total'] = intval( $entry['total'] );
							if( $entry['total'] < 0 )
								$entry['total'] = 0;
								
							return _clearStockEntry($entry);
						}
					} else if( $palette != null ){					
						// only palette and location != null
						$vPalette = $palette;
						if( $palette == "NULL" ) $vPalette = null;
							
						if( $entry['location'] != null && $entry['palette'] == $vPalette ){								
							$entry['income'] = intval( $entry['income'] );
							$entry['outgo'] = intval( $entry['outgo'] );
							$entry['total'] = intval( $entry['total'] );
							if( $entry['total'] < 0 )
								$entry['total'] = 0;
								
							return _clearStockEntry($entry);
						}					
					}
				}
					
			}
				
		}
		return array( 'income' => 0, 'outgo' => 0, 'total' => 0 );
	}
	
	function db_getStockInfo($warehouse, $category, $location, $palette){
		$stockMale = _dbGetStockInfo( $warehouse, $category, $location, $palette, true, false, false );
		$stockFemale = _dbGetStockInfo( $warehouse, $category, $location, $palette, false, true, false );
		$stockBaby = _dbGetStockInfo( $warehouse, $category, $location, $palette, false, false, true );
		$stockUnisex = _dbGetStockInfo( $warehouse, $category, $location, $palette, true, true, false );
		$stockAsex = _dbGetStockInfo( $warehouse, $category, $location, $palette, false, false, false );
		
		$overall = $stockMale['total'] + $stockFemale['total'] + $stockBaby['total'] + $stockUnisex['total'] + $stockAsex['total'];
		
		return array( 
				'male' => $stockMale,
				'female' => $stockFemale,
				'baby' => $stockBaby,
				'unisex' => $stockUnisex,
				'asex' => $stockAsex,
				'overall' => $overall
		);
	}
	
	function _getCategoryGenderStock($warehouse, $category, $male, $female, $baby){
		// get all gender stocks
		$sql = "SELECT category, male, female, baby, SUM(income) AS income, SUM(outgo) AS outgo, SUM(income)-SUM(outgo) AS total "
				."FROM ".$GLOBALS['dbPrefix']."storages"
				." JOIN ".$GLOBALS['dbPrefix']."categories"
					." ON ".$GLOBALS['dbPrefix']."storages.category=".$GLOBALS['dbPrefix']."categories.id"
				." GROUP BY category, male, female, baby";
		$result = dbSqlCache($sql);
		
		// check for null results
		if( gettype($result) == "array" ){
			
			// find category in result
			foreach( $result as $entry ){
				if( $entry['category'] == $category
						&& $male == $entry['male']
						&& $female == $entry['female']
						&& $baby == $entry['baby'] ){
							
					$entry['income'] = intval( $entry['income'] );
					$entry['outgo'] = intval( $entry['outgo'] );
					$entry['total'] = intval( $entry['total'] );
					if( $entry['total'] < 0 )
						$entry['total'] = 0;
						
					return $entry;
					
				}
			}
			
		}
		return array( 'income' => 0, 'outgo' => 0, 'total' => 0);
	}
	
	function db_getCategoryStockInfo($warehouse, $category){
		$stockMale = _getCategoryGenderStock( $warehouse, $category, true, false, false );
		$stockFemale = _getCategoryGenderStock( $warehouse, $category, false, true, false );
		$stockBaby = _getCategoryGenderStock( $warehouse, $category, false, false, true );
		$stockUnisex = _getCategoryGenderStock( $warehouse, $category, true, true, false );
		$stockAsex = _getCategoryGenderStock( $warehouse, $category, false, false, false );
		$categoryObj = db_getCategory( $warehouse, $category );

		$overall = $stockMale['total'] + $stockFemale['total'] + $stockBaby['total'] + $stockUnisex['total'] + $stockAsex['total'];
		$demand = $categoryObj['required'];
		
		return array( 	'male' => $stockMale,
						'female' => $stockFemale,
						'baby' => $stockBaby,
						'unisex' => $stockUnisex,
						'asex' => $stockAsex,
						'overall' => $overall,
						'demand' => $demand
		);
	}
	
	function db_getPaletteStockInfo($palette){
		$sql = "SELECT category, carton, SUM(income) AS income, SUM(outgo) AS outgo, SUM(income)-SUM(outgo) AS total "
				."FROM ".$GLOBALS['dbPrefix']."storages"
				." JOIN ".$GLOBALS['dbPrefix']."categories"
					." ON ".$GLOBALS['dbPrefix']."storages.category=".$GLOBALS['dbPrefix']."categories.id"
				." WHERE palette=".$palette." GROUP BY category";
		return dbSqlCache($sql);
	}
	
	function db_getUnlocatedPalettesStockInfo($warehouseId, $category){
		$sql = "SELECT ".$GLOBALS['dbPrefix']."palettes.name AS paletteName, ".$GLOBALS['dbPrefix']."palettes.id AS paletteId"
				." FROM ".$GLOBALS['dbPrefix']."storages JOIN ".$GLOBALS['dbPrefix']."palettes"
				." ON ".$GLOBALS['dbPrefix']."storages.palette=".$GLOBALS['dbPrefix']."palettes.id"
				." WHERE location IS NULL"
				." GROUP BY PALETTE";
		$palettes = dbSqlCache($sql);
		
		// get stock info for palettes
		if( $palettes ){
		
			$stock = array();
			$GLOBALS['show'] = true;
			foreach( $palettes as $palette ){
				array_push( $stock, array(
						'id' => $palette['paletteId'],
						'name' => $palette['paletteName'],
						'stock' => db_getStockInfo( $warehouseId, $category, "NULL", $palette['paletteId'] )
				) );
			}
			unset($GLOBALS['show']);
			
			return $stock;
				
		}
		
		return false;
	}
	
	function db_getLocationStockInfo($location){
		$sql = "SELECT category, carton, SUM(income) AS income, SUM(outgo) AS outgo, SUM(income)-SUM(outgo) AS total "
				."FROM ".$GLOBALS['dbPrefix']."storages"
				." JOIN ".$GLOBALS['dbPrefix']."categories"
						." ON ".$GLOBALS['dbPrefix']."storages.category=".$GLOBALS['dbPrefix']."categories.id"
				." WHERE location=".$location." GROUP BY category";
		$categories = dbSqlCache($sql);
		$palettes = db_getPalettesAtLocation( $location );
		return array( 'categories' => $categories, 'palettes' => $palettes );
	}
	
	function db_getPalettesAtLocation($location){
		$sql = "SELECT ".$GLOBALS['dbPrefix']."palettes.name, ".$GLOBALS['dbPrefix']."palettes.id"
				." FROM ".$GLOBALS['dbPrefix']."palettes"
				." JOIN ".$GLOBALS['dbPrefix']."storages"
					." ON ".$GLOBALS['dbPrefix']."palettes.id=".$GLOBALS['dbPrefix']."storages.palette"
				." JOIN ".$GLOBALS['dbPrefix']."locations"
					." ON ".$GLOBALS['dbPrefix']."storages.location=".$GLOBALS['dbPrefix']."locations.id"
				." WHERE ".$GLOBALS['dbPrefix']."storages.location=".$location
				." GROUP BY ".$GLOBALS['dbPrefix']."palettes.name";
		return dbSqlCache($sql);
	}

?>
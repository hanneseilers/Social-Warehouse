<?php

	function db_addToStock($category, $location, $palette, $income, $outgo, $male, $female, $baby, $estimated){
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
		return dbSQL($sql);
	}
	
	function db_getStockInfo($category, $location, $palette){
		
	}

?>
<?php
	
	if( isset($_SESSION['warehouseinfo']) && isset($_GET['function']) ){
		
		if( $_GET['function'] == "addToStock"
				&& isset($_GET['category'])
				&& isset($_GET['location'])
				&& isset($_GET['palette'])
				&& isset($_GET['in'])
				&& isset($_GET['out'])
				&& isset($_GET['male'])
				&& isset($_GET['female'])
				&& isset($_GET['baby']) ){
			
			if( db_addToStock( $_SESSION['warehouseinfo']['id'], 
					$_GET['category'],
					$_GET['location'],
					$_GET['palette'],
					$_GET['in'],
					$_GET['out'],
					$_GET['male'],
					$_GET['female'],
					$_GET['baby']) )
				
				print $GLOBALS['OK'];
			else
				print $GLOBALS['ERR'];
		}
		
		if( $_GET['function'] == "getPaletteStockInfo" && isset($_GET['palette']) ){			
			print json_encode( db_getPaletteStockInfo($_GET['palette']) );
		}
		
		if( $_GET['function'] == "getLocationStockInfo" && isset($_GET['location']) ){
			print json_encode( db_getLocationStockInfo($_GET['location']) );
		}
		
		if( isset($_SESSION['warehouseinfo']) && $_GET['function'] == "getCategoryStockInfo" && isset($_GET['category']) ){
			print json_encode( getRecursiveStockInfo( $_SESSION['warehouseinfo']['id'], $_GET['category']) );
		}
		
		if( $_GET['function'] == "getStockInfo" && isset($_GET['category']) ){
			
			if( isset($_GET['location']) && isset($_GET['palette']) )
				print json_encode( db_getStockInfo($_SESSION['warehouseinfo']['id'], $_GET['category'], $_GET['location'], $_GET['palette']) );
			
			else if( isset($_GET['location']) )
				print json_encode( db_getStockInfo($_SESSION['warehouseinfo']['id'], $_GET['category'], $_GET['location'], null) );
			
			else if( isset($_GET['palette']) )
				print json_encode( db_getStockInfo($_SESSION['warehouseinfo']['id'], $_GET['category'], null, $_GET['palette']) );
		}
		
		if( $_GET['function'] == "getUnlocatedPalettesStockInfos" && isset($_GET['category']) ){
			print json_encode( db_getUnlocatedPalettesStockInfo($_SESSION['warehouseinfo']['id'], $_GET['category']) );
		}
		
		if( $_GET['function'] == "getStockAtLocation" && isset($_GET['category']) && isset($_GET['location']) ){
			print json_encode( db_getStockAtLocation(
					$_SESSION['warehouseinfo']['id'],
					$_GET['category'],
					$_GET['location']) );
		}
	}
?>
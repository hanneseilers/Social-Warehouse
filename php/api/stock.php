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
			
			if( db_addToStock( $_GET['category'],
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
			print json_encode( db_getPlaetteStockInfo($_GET['palette']) );
		}
		
		if( $_GET['function'] == "getLocationStockInfo" && isset($_GET['location']) ){
			print json_encode( db_getLocationStockInfo($_GET['location']) );
		}
		
	}
?>
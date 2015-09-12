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
				&& isset($_GET['baby'])
				&& isset($_GET['estimated']) ){
			
			if( db_addToStock( $_GET['category'],
					$_GET['location'],
					$_GET['palette'],
					$_GET['in'],
					$_GET['out'],
					$_GET['male'],
					$_GET['female'],
					$_GET['baby'],
					$_GET['estimated']) )
				
				print $OK;
			else
				print $ERR;
		}
		
	}
?>
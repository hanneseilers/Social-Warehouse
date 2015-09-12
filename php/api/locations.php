<?php

	if( isset($_SESSION['warehouseinfo']) && isset($_GET['function']) ){
		
		if( $_GET['function'] == "getLocations" ){
			print json_encode( db_getLocations($_SESSION['warehouseinfo']['id']) );
		}
				
	}
	
?>
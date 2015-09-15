<?php

	if( isset($_SESSION['warehouseinfo']) && isset($_GET['function']) ){
		
		if( $_GET['function'] == "getLocations" ){
			print json_encode( db_getLocations($_SESSION['warehouseinfo']['id']) );
		}
		
		if( $_GET['function'] == "editLocation" && isset($_GET['id']) && isset($_GET['name']) ){
			if( db_editLocation($_GET['id'], base64_decode($_GET['name'])) )
				print $GLOBALS['OK'];
			else
				print $GLOBALS['ERR'];
		}
		
		if( $_GET['function'] == "addLocation" && isset($_GET['name']) ){
			if( db_addLocation($_SESSION['warehouseinfo']['id'], base64_decode($_GET['name'])) )
				print $GLOBALS['OK'];
			else
				print $GLOBALS['ERR'];
		}
		
		if( $_GET['function'] == "deleteLocation" && isset($_GET['id']) ){
			if( db_deleteLocation($_SESSION['warehouseinfo']['id'], $_GET['id']) )
				print $GLOBALS['OK'];
			else
				print $GLOBALS['ERR'];
		}
				
	}
	
?>
<?php

	if( isset($_SESSION['warehouseinfo']) && isset($_GET['function']) ){
	
		if( $_GET['function'] == "addPalette" && isset($_GET['name'])  ){
			if( db_addPalette($_SESSION['warehouseinfo']['id'], base64_decode($_GET['name'])) )
				print $GLOBALS['OK'];
			else
				print $GLOBALS['ERR'];
		}
		
		if( $_GET['function'] == "deletePalette" && isset($_GET['id'])  ){
			if( db_deletePalette($_SESSION['warehouseinfo']['id'], $_GET['id']) )
				print $GLOBALS['OK'];
			else
				print $GLOBALS['ERR'];
		}
		
		if( $_GET['function'] == "clearPalette" && isset($_GET['id'])  ){
			if( db_clearPalette($_SESSION['warehouseinfo']['id'], $_GET['id']) )
				print $GLOBALS['OK'];
			else
				print $GLOBALS['ERR'];
		}
		
		if( $_GET['function'] == "editPalette" && isset($_GET['id']) && isset($_GET['name'])  ){
			if( db_editPalette($_SESSION['warehouseinfo']['id'], $_GET['id'], base64_decode($_GET['name'])) )
				print $GLOBALS['OK'];
			else
				print $GLOBALS['ERR'];
		}
		
		if( $_GET['function'] == "getPalettes"  ){
			print json_encode( db_getPalettes($_SESSION['warehouseinfo']['id']) );
		}
		
		if( $_GET['function'] == "movePalette" && isset($_GET['palette']) && isset($_GET['location'])  ){
			if( db_movePalette($_GET['palette'], $_GET['location']) )
				print $GLOBALS['OK'];
			else
				print $GLOBALS['ERR'];
		}
			
	}

?>
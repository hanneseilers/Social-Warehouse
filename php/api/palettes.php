<?php

	if( isset($_SESSION['warehouseinfo']) && isset($_GET['function']) ){
	
		if( $_GET['function'] == "addPalette" && isset($_GET['name'])  ){
			if( db_addPalette($_SESSION['warehouseinfo']['id'], base64_decode($_GET['name'])) )
				print $OK;
			else
				print $ERR;
		}
		
		if( $_GET['function'] == "deletePalette" && isset($_GET['id'])  ){
			if( db_deletePalette($_SESSION['warehouseinfo']['id'], $_GET['id']) )
				print $OK;
			else
				print $ERR;
		}
		
		if( $_GET['function'] == "editPalette" && isset($_GET['id']) && isset($_GET['name'])  ){
			if( db_editPalette($_SESSION['warehouseinfo']['id'], $_GET['id'], base64_decode($_GET['name'])) )
				print $OK;
			else
				print $ERR;
		}
		
		if( $_GET['function'] == "getPalettes"  ){
			print json_encode( db_getPalettes($_SESSION['warehouseinfo']['id']) );
		}
			
	}

?>
<?php

	if( isset($_SESSION['warehouseinfo']) && isset($_GET['function']) ){
		
		if( $_GET['function'] == "addCategory" && isset($_GET['name']) && isset($_GET['name']) ){			
			$result = db_addCateory( $_SESSION['warehouseinfo']['id'], base64_decode($_GET['name']), $_GET['parent'] );
			if( $result )
				print $GLOBALS['OK'];
			else
				print $GLOBALS['ERR'];
		}
		
		if( $_GET['function'] == "deleteCategory" && isset($_GET['id']) ){
			if( db_deleteCategory($_SESSION['warehouseinfo']['id'], $_GET['id']) )
				print $GLOBALS['OK'];
			else
				print $GLOBALS['ERR'];
		}
		
		if( $_GET['function'] == "getCategories" && isset($_GET['location']) && isset($_GET['palette']) ){
			print json_encode( db_getCategories($_SESSION['warehouseinfo']['id'], $_GET['location'], $_GET['palette']) );
		}
		
		if( $_GET['function'] == "editCategory" && isset($_GET['id']) && isset($_GET['name'])
				&& isset($_GET['demand']) && isset($_GET['carton']) && isset($_GET['showDemand']) ){
			if( db_editCategory($_GET['id'], base64_decode($_GET['name']), $_GET['demand'], $_GET['carton'], $_GET['showDemand']) )
				print $GLOBALS['OK'];
			else
				print $GLOBALS['ERR'];
		}
		
	}
	
?>
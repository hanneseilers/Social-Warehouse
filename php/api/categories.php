<?php

	if( isset($_SESSION['warehouseinfo']) && isset($_GET['function']) ){
		
		if( $_GET['function'] == "addCategory" && isset($_GET['name']) && isset($_GET['parent']) ){
			if( $_GET['parent'] == '' )
				$_GET['parent'] = "NULL";
			
			if( db_addCateory( $_SESSION['warehouseinfo']['id'], base64_decode($_GET['name']), $_GET['parent'] ) )
				print $OK;
			else
				print $ERR;
		}
		
		if( $_GET['function'] == "deleteCategory" && isset($_GET['id']) ){
			if( db_deleteCategory($_GET['id']) )
				print $OK;
			else
				print $ERR;
		}
		
		if( $_GET['function'] == "getCategories" ){
			print json_encode( db_getCategories($_SESSION['warehouseinfo']['id']) );
		}
		
		if( $_GET['function'] == "editCategory" && isset($_GET['id']) && isset($_GET['name']) && isset($_GET['demand']) ){
			if( db_editCategory($_GET['id'], base64_decode($_GET['name']), $_GET['demand']) )
				print $OK;
			else
				print $ERR;
		}
		
	}

?>
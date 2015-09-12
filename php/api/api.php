<?php
	/*
	 * API calls
	 * Set GET parameter function to call api function.
	 */

	session_start();
	include( "../db/db.php" );
	
	$OK = "ok";
	$ERR = "err";
	$SEP = ";";
	
	// include sup api fiels
	include( "categories.php" );
	include( "warehouses.php" );
	include( "locations.php" );
	
	function _updateWarehouseInfo($id){
		$_SESSION['warehouseinfo'] = db_getWarehouseInfo( $id )[0];
	}
	
	/* 
	 * check if login data is valid
	 * warehouse = id
	 * pw = md5 password
	 * @return = <status>;<warehouse-id>
	*/
	if( $_GET['function'] == "checkLogin" ){
		if( db_checkWarehouseLogin($_GET['warehouse'], $_GET['pw']) ){
			_updateWarehouseInfo( $_GET['warehouse'] );
			print $OK;
		} else {
			print $ERR;
		}
		
		print $SEP.$_GET['warehouse'];
	}
	
	/*
	 * Logout and destory session 
	 */
	if( $_GET['function'] == "logout" ){
		session_destroy();
	}
	
?>
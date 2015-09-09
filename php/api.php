<?php
	/*
	 * API calls
	 * Set GET parameter function to call api function.
	 */

	session_start();
	include( "db/db.php" );
	
	$OK = "ok";
	$ERR = "err";
	$SEP = ";";
	
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
	
	/*
	 * Add a new warehouse
	 * name = warehouse name
	 * desc = description
	 * pw = md5 password
	 * @return = <status>;<warehouse-id>
	 */
	if( $_GET['function'] == "addWarehouse" ){
		$id = db_addWarehouse( base64_decode($_GET['name']), base64_decode($_GET['desc']), $_GET['pw'] );
		if( $id > 0 )
			print $OK.$SEP.$id;
		else
			print $ERR;
	}
	
	/*
	 * Deletes the current warehouse
	 * @return = <status>
	 */
	if( isset($_SESSION['warehouseinfo']) && $_GET['function'] == "deleteWarehouse" ){
		if( db_deleteWarehouse($_SESSION['warehouseinfo']['id']) ){
			print $OK;
			session_destroy();
		} else{
			print $ERR;
		}
	}
	
	/*
	 * Change warehouse name
	 * name = new warehouse name
	 * @return = <status>
	 */
	if( isset($_SESSION['warehouseinfo']) && $_GET['function'] == "changeWarehouseInfo" ){
		if( db_changeWarehouseInfo($_SESSION['warehouseinfo']['id'], base64_decode($_GET['name']), base64_decode($_GET['desc']), $_GET['pw']) ){
			_updateWarehouseInfo( $_SESSION['warehouseinfo']['id'] );
			print $OK;
		} else {
			print $ERR;
		}
	}
	
	/*
	 * Gets warehouse description
	 * warehouse = warehouse-id
	 * @return = <status>;<warehouse description>
	 */
	if( $_GET['function'] == "getWarehouseDescription" ){
		$result = db_getWarehouseDescription( $_GET['warehouse'] );
		if( strlen($result) > 0 ){
			print $OK.$SEP.$result;
		} else {
			print $ERR;
		}
	}
	
?>
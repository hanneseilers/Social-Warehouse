<?php
/*
 * Add a new warehouse
 * name = warehouse name
 * desc = description
 * pw = md5 password
 * country = warehouse country
 * city = warehouse city
 * @return = <status>;<warehouse-id>
 */
if( $_GET['function'] == "addWarehouse" ){
	$id = db_addWarehouse( base64_decode($_GET['name']), base64_decode($_GET['desc']), $_GET['pw'], base64_decode($_GET['country']), base64_decode($_GET['city']) );
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
 * pw = new md5 password
 * country = warehouse country
 * city = warehouse city
 * @return = <status>
 */
if( isset($_SESSION['warehouseinfo']) && $_GET['function'] == "changeWarehouseInfo" ){
	if( db_changeWarehouseInfo($_SESSION['warehouseinfo']['id'], base64_decode($_GET['name']), base64_decode($_GET['desc']), $_GET['pw'], base64_decode($_GET['country']), base64_decode($_GET['city'])) ){
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
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
if( $_GET['function'] == "addWarehouse"
		&& isset($_GET['name'])
		&& isset($_GET['desc'])
		&& isset($_GET['pw'])
		&& isset($_GET['country'])
		&& isset($_GET['city'])
		&& isset($_GET['mail']) ){
	
	if( db_addWarehouse( base64_decode($_GET['name']),
			base64_decode($_GET['desc']),
			$_GET['pw'],
			base64_decode($_GET['country']),
			base64_decode($_GET['city']),
			base64_decode($_GET['mail']) ) )
		print $GLOBALS['OK'];
	else
		print $GLOBALS['ERR'];
}

/*
 * Deletes the current warehouse
 * @return = <status>
 */
if( isset($_SESSION['warehouseinfo']) && $_GET['function'] == "deleteWarehouse" ){
	if( db_deleteWarehouse($_SESSION['warehouseinfo']['id']) ){
		print $GLOBALS['OK'];
		session_destroy();
	} else{
		print $GLOBALS['ERR'];
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
if( isset($_SESSION['warehouseinfo']) && $_GET['function'] == "editWarehouse"
		&& isset($_GET['name'])
		&& isset($_GET['desc'])
		&& isset($_GET['pw'])
		&& isset($_GET['country'])
		&& isset($_GET['city'])
		&& isset($_GET['mail'])
		&& isset($_GET['locationLess'])
		&& isset($_GET['paletteLess']) ){
	if( db_editWarehouse($_SESSION['warehouseinfo']['id'],
			base64_decode($_GET['name']),
			base64_decode($_GET['desc']),
			$_GET['pw'],
			base64_decode($_GET['country']),
			base64_decode($_GET['city']),
			base64_decode($_GET['mail']),
			$_GET['locationLess'],
			$_GET['paletteLess']) ){
		_updateWarehouseInfo( $_SESSION['warehouseinfo']['id'] );
		print $GLOBALS['OK'];
	} else {
		print $GLOBALS['ERR'];
	}
}

/*
 * Changes password for restricted access
 */
if( isset($_SESSION['warehouseinfo']) && $_GET['function'] == "editRestrictedPassword" && isset($_GET['pw']) ){
	if( isset($_SESSION['restricted']) && $_SESSION['restricted'] ){
		print $GLOBALS['ERR'];
	} else {
		
		if( db_editWarehouseRestrictedPassword($_SESSION['warehouseinfo']['id'], $_GET['pw']) ){
			print $GLOBALS['OK'];
		} else {
			print $GLOBALS['ERR'];
		}
		
	}
}

/*
 * Get warehouse data
 * id = warehouse id
 * @return = warehouse json data
 */
if( isset($_SESSION['warehouseinfo']) && $_GET['function'] == "getWarehouse" && isset($_GET['id']) ){
	$result = db_getWarehouseInfo( $_GET['id'] );
	if( $result )
		print json_encode($result[0]);
	else
		print $ERR;
}

/*
 * Gets warehouse description
 * warehouse = warehouse-id
 * @return = <status>;<warehouse description>
 */
if( $_GET['function'] == "getWarehouseDescription" ){
	$result = db_getWarehouseDescription( $_GET['warehouse'] );
	if( strlen($result) > 0 ){
		print $GLOBALS['OK'].$GLOBALS['SEP'].$result;
	} else {
		print $GLOBALS['ERR'];
	}
}
?>
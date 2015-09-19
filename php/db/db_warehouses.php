<?php
function db_getWarehouses(){
	$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."warehouses ORDER BY name ASC";
	return dbSQL($sql);
}

function db_checkWarehouseLogin($id, $password){
	// check admin password
	$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."warehouses WHERE id=".$id
			." AND password='".$password."'";
	$result = dbSQL($sql);
	if( count($result) > 0 )
		return True;

	return False;
}

function db_checkWarehouseLoginRestricted($id, $password){
	// check restricted password
	$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."warehouses WHERE id=".$id
	." AND passwordRestricted='".$password."'";
	$result = dbSQL($sql);
	if( count($result) > 0 )
		return True;

	return False;
}

function db_getWarehouseInfo($id){
	$sql = "SELECT id, name, description, country, city, mail, disableLocationLess, disablePaletteLess FROM ".$GLOBALS['dbPrefix']."warehouses WHERE id=".$id;
	return dbSQL($sql);
}

function db_editWarehouse($id, $name, $description, $password, $country, $city, $mail, $locationLess, $paletteLess){
	$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."warehouses WHERE name='".$name."' AND country='".$country."' AND city='".$city."'";
	if( count(dbSQL($sql)) > 0 ){
		
		if( strlen($password) > 0 ){
			$sql = "UPDATE ".$GLOBALS['dbPrefix']."warehouses SET"
					." name='".$name."'"
					.", description='".$description."'"
					.", country='".$country."'"
					.", city='".$city."'"
					.", mail='".$mail."'"
					.", disableLocationLess=".$locationLess
					.", disablePaletteLess=".$paletteLess
					.", password='".$password."'"
					." WHERE id=".$id;
		} else{
			$sql = "UPDATE ".$GLOBALS['dbPrefix']."warehouses SET"
					." name='".$name."'"
					.", description='".$description."'"
					.", country='".$country."'"
					.", city='".$city."'"
					.", mail='".$mail."'"
					.", disableLocationLess=".$locationLess
					.", disablePaletteLess=".$paletteLess
					." WHERE id=".$id;
		}
		return dbSQL($sql);
		
	}
	
	return false;
}

function db_addWarehouse($name, $description, $password, $country, $city, $mail){
	global $mail_from;
	
	$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."warehouses WHERE name='".$name."' AND country='".$country."' AND city='".$city."'";
	if( count(dbSQL($sql)) == 0 ){
		$sql = "INSERT INTO ".$GLOBALS['dbPrefix']."warehouses (name, description, password, country, city, mail) "
				."VALUES ('".$name."', '".$description."', '".$password."', '".$country."', '".$city."', '".$mail."')";
		if( dbSQL($sql) ){
			
			// send mail
			include( "../lang/lang.php" );
			$message = LANG('mail_registered_text');
			$warehousename = $country." - ".$city.": ".$name;
			$message = preg_replace( "/%/", $warehousename, $message, 1 );
			$message = preg_replace( "/%/", "http://".$_SERVER['HTTP_HOST'], $message, 1 );
			
			send_mail( $mail_from, $mail, LANG('mail_registered_subject'), $message );
			return true;
		}
	}

	return false;
}

function db_deleteWarehouse($id){
	$sql = "SELECT ".$GLOBALS['dbPrefix']."storages.id "
			."FROM ".$GLOBALS['dbPrefix']."categories INNER JOIN ".$GLOBALS['dbPrefix']."storages "
			."ON ".$GLOBALS['dbPrefix']."storages.category=".$GLOBALS['dbPrefix']."categories.id AND ".$GLOBALS['dbPrefix']."categories.warehouse=".$id;
	$result = dbSQL($sql);
	
	// delete stoarges
	foreach( $result as $storage ){
		$sql = "DELETE FROM ".$GLOBALS['dbPrefix']."storages WHERE id=".$storage['id'];
		dbSQL($sql);
	}
	
	// delete categories
	$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."categories WHERE warehouse=".$id." ORDER BY id DESC";
	$result = dbSQL($sql);

	foreach( $result as $category ){
		$sql = "DELETE FROM ".$GLOBALS['dbPrefix']."categories WHERE id=".$category['id'];
		dbSQL($sql);
	}
	
	// delete location
	$sql = "DELETE FROM ".$GLOBALS['dbPrefix']."locations WHERE warehouse=".$id;
	print $sql."<br />";
	dbSQL($sql);
	
	// delete palettes
	$sql = "DELETE FROM ".$GLOBALS['dbPrefix']."palettes WHERE warehouse=".$id;
	dbSQL($sql);
	
	// delete warehouse
	$sql = "DELETE FROM ".$GLOBALS['dbPrefix']."warehouses WHERE id=".$id;
	return dbSQL($sql);
}

function db_getWarehouseDescription($id){
	$sql = "SELECT * FROM ".$GLOBALS['dbPrefix']."warehouses WHERE id=".$id;
	return dbSQL($sql)[0]['description'];
}
?>
<?php
function db_getWarehouses(){
	$sql = "SELECT * FROM warehouses ORDER BY name ASC";
	return dbSQL($sql);
}

function db_checkWarehouseLogin($id, $password){
	$sql = "SELECT * FROM warehouses WHERE id=".$id." AND password='".$password."'";
	$result = dbSQL($sql);
	if( count($result) > 0 )
		return True;

	return False;
}

function db_getWarehouseInfo($id){
	$sql = "SELECT * FROM warehouses WHERE id=".$id;
	return dbSQL($sql);
}

function db_editWarehouse($id, $name, $description, $password, $country, $city, $mail){
	$sql = "SELECT * FROM warehouses WHERE name='".$name."' AND country='".$country."' AND city='".$city."'";
	if( count(dbSQL($sql)) == 0 ){
		
		if( strlen($password) > 0 )
			$sql = "UPDATE warehouses "
					."SET name='".$name."', description='".$description."', "
					." password='".$password."', country='".$country."', city='".$city."' mail='".$mail."' WHERE id=".$id;
		else
			$sql = "UPDATE warehouses SET name='".$name."', description='".$description."', country='".$country."', city='".$city."' WHERE id=".$id;
		return dbSQL($sql);
		
	}
	
	return false;
}

function db_addWarehouse($name, $description, $password, $country, $city, $mail){
	global $mail_from;
	
	$sql = "SELECT * FROM warehouses WHERE name='".$name."' AND country='".$country."' AND city='".$city."'";
	if( count(dbSQL($sql)) == 0 ){
		$sql = "INSERT INTO warehouses (name, description, password, country, city, mail) "
				."VALUES ('".$name."', '".$description."', '".$password."', '".$country."', '".$city."', '".$mail."')";
		if( dbSQL($sql) ){
			
			// send mail
			include( "../lang/lang.php" );
			$message = LANG('mail_registered_text');
			$warehousename = $country." - ".$city.": ".$name;
			$message = preg_replace( "/%/", $warehousename, $message, 1 );
			$message = preg_replace( "/%/", "http://".$_SERVER['HTTP_HOST'], $message, 1 );
			$message = preg_replace( "/%/", $password, $message, 1 );
			
			send_mail( $mail_from, $mail, LANG('mail_registered_subject'), $message );
			return true;
		}
	}

	return false;
}

function db_deleteWarehouse($id){
	$sql = "SELECT storages.id FROM categories INNER JOIN storages ON storages.category=categories.id AND categories.warehouse=".$id;
	$result = dbSQL($sql);
	
	// delete stoarges
	foreach( $result as $storage ){
		$sql = "DELETE FROM storages WHERE id=".$storage['id'];
		dbSQL($sql);
	}
	
	// delete categories
	$sql = "SELECT * FROM categories WHERE warehouse=".$id." ORDER BY id DESC";
	$result = dbSQL($sql);

	foreach( $result as $category ){
		$sql = "DELETE FROM categories WHERE id=".$category['id'];
		dbSQL($sql);
	}
	
	// delete location
	$sql = "DELETE FROM locations WHERE warehouse=".$id;
	print $sql."<br />";
	dbSQL($sql);
	
	// delete palettes
	$sql = "DELETE FROM palettes WHERE warehouse=".$id;
	dbSQL($sql);
	
	// delete warehouse
	$sql = "DELETE FROM warehouses WHERE id=".$id;
	return dbSQL($sql);
}

function db_getWarehouseDescription($id){
	$sql = "SELECT * FROM warehouses WHERE id=".$id;
	return dbSQL($sql)[0]['description'];
}
?>
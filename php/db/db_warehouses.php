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

function db_editWarehouse($id, $name, $description, $password, $country, $city){
	$sql = "SELECT * FROM warehouses WHERE name='".$name."' AND country='".$country."' AND city='".$city."'";
	if( count(dbSQL($sql)) == 0 ){
		
		if( strlen($password) > 0 )
			$sql = "UPDATE warehouses SET name='".$name."', description='".$description."', password='".$password."', country='".$country."', city='".$city."' WHERE id=".$id;
		else
			$sql = "UPDATE warehouses SET name='".$name."', description='".$description."', country='".$country."', city='".$city."' WHERE id=".$id;
		return dbSQL($sql);
		
	}
	
	return false;
}

function db_addWarehouse($name, $description, $password, $country, $city){
	$sql = "SELECT * FROM warehouses WHERE name='".$name."' AND country='".$country."' AND city='".$city."'";
	if( count(dbSQL($sql)) == 0 ){
		$sql = "INSERT INTO warehouses (name, description, password, country, city) VALUES ('".$name."', '".$description."', '".$password."', '".$country."', '".$city."')";
		if( dbSQL($sql) ){
			$sql = "SELECT id FROM warehouses WHERE name='".$name."' AND description='".$description."'AND password='".$password."'";
			return dbSQL($sql)[0]['id'];
		}
	}

	return -1;
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
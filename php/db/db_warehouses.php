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

function db_changeWarehouseInfo($id, $name, $description, $password, $country, $city){
	if( strlen($password) > 0 )
		$sql = "UPDATE warehouses SET name='".$name."', description='".$description."', password='".$password."', country='".$country."', city='".$city."' WHERE id=".$id;
	else
		$sql = "UPDATE warehouses SET name='".$name."', description='".$description."', country='".$country."', city='".$city."' WHERE id=".$id;
	return dbSQL($sql);
}

function db_addWarehouse($name, $description, $password, $country, $city){
	$sql = "SELECT * FROM warehouses WHERE name='".$name."'";
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
	$sql = "DELETE FROM warehouses WHERE id=".$id;
	return dbSQL($sql);
}

function db_getWarehouseDescription($id){
	$sql = "SELECT * FROM warehouses WHERE id=".$id;
	return dbSQL($sql)[0]['description'];
}
?>
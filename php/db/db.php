<?php

include( "config.php" );

/**
 * Connect to database and execute SQL command.
 * @param string $sql	SQL command to execute.
 * @return array
 */
function dbSQL($sql){	
	global $dbDatabase, $dbUser, $dbPassword, $dbHost, $log_enabled;
	
	$vHandle = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbDatabase);
	$vResult = mysqli_query($vHandle, $sql);
	
	if( $log_enabled ) print "<hr />".$sql."<hr />";

	if( gettype($vResult) == "boolean" ) return $vResult;
	else{
		$vReturn = array();
		while( ($row=mysqli_fetch_assoc($vResult)) ){
			array_push($vReturn, $row);	
		}
		return $vReturn;
	}
}

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

function db_changeWarehouseInfo($id, $name, $description, $password){
	if( strlen($password) > 0 )
		$sql = "UPDATE warehouses SET name='".$name."', description='".$description."', password='".$password."' WHERE id=".$id;
	else 
		$sql = "UPDATE warehouses SET name='".$name."', description='".$description."' WHERE id=".$id;
	return dbSQL($sql);
}

function db_addWarehouse($name, $description, $password){
	$sql = "SELECT * FROM warehouses WHERE name='".$name."'";
	if( count(dbSQL($sql)) == 0 ){		
		$sql = "INSERT INTO warehouses (name, description, password) VALUES ('".$name."', '".$description."', '".$password."')";
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

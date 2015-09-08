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

function db_getGroups(){
	$sql = "SELECT * FROM groups ORDER BY name ASC";
	return dbSQL($sql);
}

function db_checkGroupLogin($id, $password){
	$sql = "SELECT * FROM groups WHERE id=".$id." AND password='".$password."'";
	$result = dbSQL($sql);
	if( count($result) > 0 )
		return True;
	
	return False;
}

function db_getGroupInfo($id){
	$sql = "SELECT * FROM groups WHERE id=".$id;
	return dbSQL($sql);
}

function db_changeGroupInfo($id, $name, $description, $password){
	if( strlen($password) > 0 )
		$sql = "UPDATE groups SET name='".$name."', description='".$description."', password='".$password."' WHERE id=".$id;
	else 
		$sql = "UPDATE groups SET name='".$name."', description='".$description."' WHERE id=".$id;
	return dbSQL($sql);
}

function db_addGroup($name, $description, $password){
	$sql = "SELECT * FROM groups WHERE name='".$name."'";
	if( count(dbSQL($sql)) == 0 ){		
		$sql = "INSERT INTO groups (name, description, password) VALUES ('".$name."', '".$description."', '".$password."')";
		if( dbSQL($sql) ){
			$sql = "SELECT id FROM groups WHERE name='".$name."' AND description='".$description."'AND password='".$password."'";
			return dbSQL($sql)[0]['id'];
		}
	}
	
	return -1;
}
?>

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

function db_changeGroupName($id, $name){
	$sql = "UPDATE groups SET name='".$name."' WHERE id=".$id;
	return dbSQL($sql);
}

?>

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

// include sub php files
include( "db_warehouses.php" );
include( "db_categories.php" );
include( "db_locations.php" );
include( "db_palettes.php" );

?>

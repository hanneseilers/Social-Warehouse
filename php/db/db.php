<?php

include( "config.php" );

/**
 * Connect to database and execute SQL command.
 * @param string $sql	SQL command to execute.
 * @return array
 */
function dbSQL($sql){
	// create connection object
	$vReturn = false;
	
	if( !isset($GLOBALS['db']) )
		$GLOBALS['db'] = new mysqli( $GLOBALS['dbHost'], $GLOBALS['dbUser'], $GLOBALS['dbPassword'], $GLOBALS['dbDatabase'] );
	
	if( mysqli_connect_errno() == 0 ){
	
		// send query
		$vResult = $GLOBALS['db']->query( $sql );
		if( $GLOBALS['log_enabled'] ) print "<hr />".$sql."<hr />";

		// check result
		if( gettype($vResult) == "boolean" ){
			$vReturn = $vResult;
		}
		else {
			$vReturn = array();
			while( ($vRow = $vResult->fetch_assoc()) ){
				array_push($vReturn, $vRow);
			}
			$vResult->close();
		}

	}
	
	return $vReturn;
}

function dbSqlCache($sql){
	if( !isset($GLOBALS['sqlCache']) )
		$GLOBALS['sqlCache'] = [];
	
	if( isset($GLOBALS['sqlCache'][$sql]) ){
		return $GLOBALS['sqlCache'][$sql];
	}
	
	$result = dbSQL($sql);
	$GLOBALS['sqlCache'][$sql] = $result;
	return $result;
}

// include sub php files
include( "db_warehouses.php" );
include( "db_categories.php" );
include( "db_locations.php" );
include( "db_palettes.php" );
include( "db_stock.php" );

?>

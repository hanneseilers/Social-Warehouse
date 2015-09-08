<?php
	session_start();
	include( "db.php" );
	
	print $_GET['group'];
	print ";";
	
	if( db_checkGroupLogin($_GET['group'], $_GET['pw']) ){
		$_SESSION['groupid'] = $_GET['group'];
		print "ok";
	} else {
		print "failed";
	}
?>
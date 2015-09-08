<?php
	include( "db.php" );
	
	print $_GET['group'];
	print ";";
	
	if( db_checkGroupLogin($_GET['group'], $_GET['pw']) )
		print "ok";
	else
		print "failed";
?>
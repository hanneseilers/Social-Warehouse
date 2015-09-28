<html>
<head rofile="http://www.w3.org/2005/10/profile">
<title>Social Warehouse</title>

<link rel="icon" 
      type="image/png" 
      href="favicon.png">

<link type="text/css" rel="stylesheet" href="style.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.scrollto/2.1.0/jquery.scrollTo.min.js"></script> <!--  Source: https://github.com/flesler/jquery.scrollTo -->
<script src="js/md5.js"></script>
<script src="js/base64.js"></script>
<script src="js/lang.js"></script>
<script src="js/base.js"></script>
<script src="js/demand.js"></script>
<script src="js/warehouses.js"></script>
<script src="js/locations.js"></script>
<script src="js/palettes.js"></script>
<script src="js/stock.js"></script>
<script src="js/statistics.js"></script>

<meta charset="utf-8">
</head>

<?php

	// start/resume session
	session_start();
	
	// create body tag depending if logged in or not
	if( isset($_SESSION['warehouseinfo']) )
		print "<body onload='javascript: startCacheTimer();'>";
	else 
		print "<body>";
	
	print "<span id='gc_maxtime' class='hidetext'>".ini_get("session.gc_maxlifetime")."</span>";
?>

<div class="mainframe">

	<?php
		include( "db/db.php" );
	
		// include multilanguage support
		include( "lang/lang.php" );
		
		// include timedout messaghe
		if( isset($_GET['timeout']) && $_GET['timeout'] == 1 ){
			print "<div class='red' id='timeout_message' onclick='javascript: hideTimedoutMessage();'>".LANG('session_timed_out')."</div>";
		}
		
		// include content
		include( "countries/countries.php" );
		include( "header.php" );
		
		if( isset($_GET['demand']) ){
			include( "demand.php" );
		}else if( !isset($_SESSION['warehouseinfo']) || $_SESSION['warehouseinfo'] == null ){
			include( "warehouses.php" );
			include( "register.php" );
		} else {
			include( "warehouseheader.php" );
			include( "showdata.php" );
		}
	?>

	<div class="footer">
		Spendenverwaltung, published under GPLv2 by <a href="http://www.hanneseilers.de">Hannes Eilers</a>
	</div>
</div>

</body>
</html>
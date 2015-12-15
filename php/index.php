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

<meta charset="utf-8">
</head>
<body onLoad="javascript: load();">

<?php

	// start/resume session
	session_start();
	
	// create body tag depending if logged in or not
	if( isset($_SESSION['warehouseinfo']) && !isset($_GET['demand']) )
		print "<body onload='startCacheTimer(); loadData(".$_SESSION['warehouseinfo']['id'].");'>";
	else 
		print "<body>";
	
	print "<span id='gc_maxtime' class='hidetext'>".ini_get("session.gc_maxlifetime")."</span>";
?>

<div class="mainframe">

	<?php		
		// include multilanguage support
		include( "lang/lang.php" );
		
		// include timedout messaghe
		if( isset($_GET['timeout']) && $_GET['timeout'] == 1 ){
			print "<div class='hidetext' id='status_message' onclick='javascript: hideStatusMessage();'></div>";
		}
		
		// include content
		include( "header.php" );
	?>
	<div id="content"></div>
	<div class="footer">
		Spendenverwaltung, published under GPLv2 by <a href="http://www.hanneseilers.de">Hannes Eilers</a>
	</div>
</div>

<span id='gc_maxtime' class='hidetext'>1800</span>
</body>
</html>
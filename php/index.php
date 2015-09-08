<html>
<head>
<title>Spendenliste</title>

<link type="text/css" rel="stylesheet" href="style.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="js/md5.js"></script>
<script src="js/base64.js"></script>
<script src="js/jsfunctions.js"></script>

<meta charset="utf-8">
</head>
<body>

<div class="mainframe">

	<?php
		session_start();
	
		include( "db/db.php" );
	
		include( "header.php" );
		
		if( !isset($_SESSION['groupinfo']) || $_SESSION['groupinfo'] == null ){
			include( "groups.php" );
			include( "register.php" );
		} else {
			include( "groupheader.php" );
			include( "showdata.php" );
		}
	?>

	<div class="footer">
		Spendenverwaltung, published under GPLv2 by <a href="http://www.hanneseilers.de">Hannes Eilers</a>
	</div>
</div>

</body>
</html>
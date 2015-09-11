<html>
<head>
<title>Social Warehouse</title>

<link type="text/css" rel="stylesheet" href="style.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="js/md5.js"></script>
<script src="js/base64.js"></script>
<script src="js/jsfunctions.js"></script>
<script src="js/categories.js"></script>

<meta charset="utf-8">
</head>
<body>

<div class="mainframe">

	<?php
		session_start();
		include( "db/db.php" );
		
		// get language
		$default_language = "lang/en.php";
		if( !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ){
			$local_language = explode( ",", $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
			if( count($local_language) > 0 )
				$local_language = $local_language[0];
			$local_language = "lang/".$local_language.".php";
		}
		
		// load language file
		if( file_exists($local_language) )
			include( $local_language );
		else
			include( $default_language );
	
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
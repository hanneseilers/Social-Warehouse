<html>
<head>
	<title>Barcode</title>
</head>
<body>
<div align="center" style="font-family: sans-serif;">

<?php 

include_once 'Barcode39.php';
include( "db/db.php" );

// get data
if( isset($_GET['paletteID']) ) $paletteID = $_GET['paletteID'];
if( isset($_GET['paletteName']) ) $paletteName = $_GET['paletteName'];
if( isset($_GET['warehouseID']) ) $warehouseID = $_GET['warehouseID'];

// print data
if( isset($warehouseID) ){
	$warehouse = db_getWarehouseInfo( $warehouseID );
	if( sizeof($warehouse) > 0 )
		print "<p><h2>".$warehouse[0]['country']
			.", ".$warehouse[0]['city']
			.": ".$warehouse[0]['name']
			."</h2></p>";
}

if( isset($paletteName) ){
	print "<span style='font-size: 100pt; font-weight: bold;'>".base64_decode($paletteName)."</span>";
}

if( isset($paletteID) ){
	
	// set Barcode39 object 
	$bc = new Barcode39( "++SWP".$paletteID."++" ); 
	
	// set text size
	$bc->barcode_text_size = 5;
	
	// set barcode bar thickness (thick bars)
	$bc->barcode_bar_thick = 4;
	
	// set barcode bar thickness (thin bars)
	$bc->barcode_bar_thin = 2;
	
	// save new barcode 
	$bc->draw('cache/barcode_'.$paletteID.'.gif');
	
	// show image
	print "<br /><img src='cache/barcode_".$paletteID.".gif' />";
	
}

?>

</div>
</body>
</html>
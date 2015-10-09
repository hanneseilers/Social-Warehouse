<html>
<head>
	<title>Barcode</title>
</head>
<link type="text/css" rel="stylesheet" href="style.css">
<body>
<div class="mainframe" style="font-family: sans-serif;">

<?php 

include_once 'Barcode39.php';
include( "db/db.php" );
include( "lang/lang.php" );

// get data
if( isset($_GET['warehouseID']) ){
	
	$warehouseID = $_GET['warehouseID'];
	$locations = db_getLocations( $warehouseID );
	
	// show loactions
	if( count($locations) > 0 ){
		print "<p><b>".LANG('locations')."</b></p>";
		print "<p><table class='table'>";
		
		$first = true;
		foreach( $locations as $location ){
			$bc = new Barcode39( "++SWL".$location['id']."++" );
			$bc->draw( 'cache/barcode_location_'.$location['id'].'.gif' );
			
			if( $first )
				print "<tr><td class='centertext'><div><br />".$location['name']."<br /><img src='cache/barcode_location_".$location['id'].".gif' /><br /></div></td>";
			else
				print "<td class='centertext'><div><br />".$location['name']."<br /><img src='cache/barcode_location_".$location['id'].".gif' /><br /></div></td></tr>";
			
			$first = !$first;
		}
		
		// create reset code
		$bc = new Barcode39( "++SWL0++" );
		$bc->draw( 'cache/barcode_location_0.gif' );
		
		if( $first )
			print "<tr><td class='centertext'><div><br />".LANG('locations')." ".LANG('reset')."<br /><img src='cache/barcode_location_0.gif' /><br /></div></td>";
		else
			print "<td class='centertext'><div><br />".LANG('locations')." ".LANG('reset')."<br /><img src='cache/barcode_location_0.gif' /><br /></div></td>";
			
		print "</tr></table></p>";
	}
	
	// show palette
	
	$bc = new Barcode39( "++SWP0++" );
	$bc->draw( 'cache/barcode_palette_0.gif' );
	
	$bc = new Barcode39( "++SWMVP++" );
	$bc->draw( 'cache/barcode_palette_move.gif' );
	
	print "<p><b>".LANG('palettes')."</b></p>";
	print "<p><table class='table'>";
	print "<tr><td class='centertext'><div><br />".LANG('palettes')." ".LANG('reset')."<br /><img src='cache/barcode_palette_0.gif' /><br /></div></td>";
	print "<td class='centertext'><div><br />".LANG('move_palette')."<br /><img src='cache/barcode_palette_move.gif' /><br /></div></td></tr>";
	print "</table></p>";

}

?>

</div>
</body>
</html>
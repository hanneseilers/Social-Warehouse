<!DOCTYPE html>
<?php
	// include multilanguage support
	include( "lang/lang.php" );
	
	// load classloader
	include_once 'api/classloader.php';
?>

<html>
<head>
<meta charset="UTF-8">
<title><?php print LANG('warehouse_stock'); ?></title>
<link rel="icon" 
      type="image/png" 
      href="favicon.png">

<link type="text/css" rel="stylesheet" href="style.css">

<script type="text/javascript">
	var dom;
	function init(){
		document.getElementById( 'stock' ).appendChild( dom );
	}
</script>
</head>
<body class="stock" id="stock">
</body>
</html>
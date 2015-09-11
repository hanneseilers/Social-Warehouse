<div class="header"><img src="img/logo-48.png" /><span>Social Warehouse</span></div>
<div class="breadcrumps">
	
	<?php 
	
		// Show root
		if( isset($_SESSION['warehouseinfo']) )
			print "<a href=\"\">".$_SESSION['warehouseinfo']['name']."</a>";
		else
			print "<a href=\"?\">Home</a>";
	?>
	</a>
	
	<?php
	
		// show current catgeory or demand
		if( isset($_GET['demand']) ){
			$vWarehouse = db_getWarehouseInfo( $_GET['demand'] )[0];
			print " > <a href=\"\">Warehouse Demand: ".$vWarehouse['name']."</a>";
		}
		else if( isset($_SESSION['curCategory']) ){
			print " > ";
		}
	
	?>
	
</div>
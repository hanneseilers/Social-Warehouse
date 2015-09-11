<h1>
	Warehouse Demand
	<?php	
		if( isset($_GET['demand']) ){
			$vWarehouse = db_getWarehouseInfo( $_GET['demand'] )[0];
			print ": ".$vWarehouse['name'];
		}
	?>
</h1>
<div class="requirements">
</div>
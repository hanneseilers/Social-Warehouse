<h1>
	<?php	
		if( isset($_GET['demand']) ){
			$vWarehouse = db_getWarehouseInfo( $_GET['demand'] )[0];
			print LANG('warehouse_demand')." : ".$vWarehouse['name'];
		}
	?>
</h1>
<div class="demands">
	<?php 
		if( isset($_GET['demand']) ){
			
		}
	?>
</div>
<div class="table">
	<?php
	
		$warehouseId = $_SESSION['warehouseinfo']['id'];
		
		// print buttons
		print "<a href='javascript: showStock(".$warehouseId.");' class='button button3 table_cell'>".LANG('stock')."</a>";
		print "<a href='javascript: showLocations(".$warehouseId.");' class='button button3 table_cell'>".LANG('locations')."</a>";
		print "<a href='javascript: showPalettes(".$warehouseId.");' class='button button3 table_cell'>".LANG('palettes')."</a>";
	
	?>
</div>
<div id="loading" class="loadinggif centertext">
	<img src="img/loading.gif" />
</div>
<div id="datacontent"></div>
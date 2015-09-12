<div class="table">
	<?php
	
		$warehouseId = $_SESSION['warehouseinfo']['id'];
		$callback = "";
		$classes = "button button3 button_table_cell";
		
		if( isset($_GET['mode']) ){			
			if( $_GET['mode'] == "stock" ){				
				$callback = "showStock(".$warehouseId.", addToStock, true);";
			} else if( $_GET['mode'] == "locations" ){				
				$callback = "showLocations(".$warehouseId.");";
			} else if( $_GET['mode'] == "palettes" ){
				$callback = "showPalettes(".$warehouseId.");";
			}			
		} else {
			$classes .= " blue bigbutton";
		}
		
		// print buttons
		print "<a href=\"?mode=stock\" class=\"".$classes."\">".LANG('stock')."</a>";
		print "<a href=\"?mode=locations\" class=\"".$classes."\">".LANG('locations')."</a>";
		print "<a href=\"?mode=palettes\" class=\"".$classes."\">".LANG('palettes')."</a>";
	
	?>
</div>
<div id="loading" class="loadinggif centertext">
	<img src="img/loading.gif" />
</div>
<div id="datacontent"></div>
<?php 
	// load content
	print "<script>".$callback."</script>";
?>
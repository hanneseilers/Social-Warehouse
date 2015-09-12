<div class="table">
	<?php
	
		$warehouseId = $_SESSION['warehouseinfo']['id'];
		$callback = "";
		$classes = "button button3 button_table_cell";
		
		if( isset($_GET['mode']) ){			
			if( $_GET['mode'] == "categories" ){				
				$callback = "showCategories(".$warehouseId.", addToStock);";
			} else if( $_GET['mode'] == "palettes" ){
				$callback = "showPalettes(".$warehouseId.");";
			}			
		} else {
			$classes .= " blue bigbutton";
		}
		
		// print buttons
		print "<a href=\"?mode=categories\" class=\"".$classes."\">".LANG('categories')."</a>";
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
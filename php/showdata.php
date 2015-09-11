<div class="table">
	<?php
	
		$warehouseId = $_SESSION['warehouseinfo']['id'];
		$callback = "";
		$classes = "button button3 button_table_cell blue";
		
		if( isset($_GET['mode']) ){			
			if( $_GET['mode'] == "categories" ){
				$callback = "showCategories(".$warehouseId.");";
			} else if( $_GET['mode'] == "stock" ){
				$callback = "showStock(".$warehouseId.");";
			} else if( $_GET['mode'] == "palettes" ){
				$callback = "showPalettes(".$warehouseId.");";
			}			
		} else {
			$classes .= " bigbutton";
		}
		
		// print buttons
		print "<a href=\"?mode=categories\" class=\"".$classes."\">".$LANG['categories']."</a>";
		print "<a href=\"?mode=categories\" class=\"".$classes."\">".$LANG['stock']."</a>";
		print "<a href=\"?mode=categories\" class=\"".$classes."\">".$LANG['palettes']."</a>";
	
	?>
</div>
<div id="datacontent">
	<?php 
		// load content
		print "<script>".$callback."</script>";
	?>
</div>
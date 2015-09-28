<h1>
	<?php
	
		include( "db/db_statistics.php" );
		if( isset($_GET['demand']) ){
			$warehouse = db_getWarehouseInfo( $_GET['demand'] )[0];
			print LANG('warehouse_demand')." : ".$warehouse['name'];
		}
	?>
</h1>
<div class="demands">
	
	<div class="groupitem">
		<i><?php print LANG('legend'); ?>:</i>
		<table width="100%">
			<tr>
				<td>
					<img src="img/star-gray.png" /><img src="img/star-gray.png" /> 0-10% <?php print LANG('required'); ?><br />
				</td>
				<td>
					<img src="img/star-gray.png" /><img src="img/star-green.png" /> 10-25% <?php print LANG('required'); ?><br />
					<img src="img/star-green.png" /><img src="img/star-green.png" /> 25-40% <?php print LANG('required'); ?>
				</td>
				<td>
					<img src="img/star-gray.png" /><img src="img/star-yellow.png" /> 40-50% <?php print LANG('required'); ?><br />
					<img src="img/star-yellow.png" /><img src="img/star-yellow.png" /> 50-65% <?php print LANG('required'); ?>
				<td>
				</td>
				<td>
					<img src="img/star-gray.png" /><img src="img/star-red.png" /> 65-80% <?php print LANG('required'); ?><br />
					<img src="img/star-red.png" /><img src="img/star-red.png" /> 80-100% <?php print LANG('required'); ?>
				</td>
			</tr>
		</table>
		<br />
		<img src='img/male_s.png' /> male <img src='img/female_s.png' /> female <img src='img/baby_s.png' />children/baby <img src='img/unisex_s.png' />unisex <img src='img/asex_s.png' />asexual
	</div>

	<div class="groupitem">
	<?php 
		if( isset($_GET['demand']) ){
			
			// get sorted category hierarchy
			$categories = db_getCategories( $warehouse['id'], "NULL", "NULL" );
			$hierarchieStrings = getCategoryHierarchyStrings( $categories );
			array_multisort( $hierarchieStrings, SORT_ASC );
			$table_closed = true;
			$highlight = true;
			
			foreach( $hierarchieStrings as $hierarchyEntry ){
				
				// set information about category
				$category = $hierarchyEntry['hierarchy'][0];
				$categoryId = $category['id'];
				$stock = getRecursiveStockInfo( $warehouse['id'], $categoryId );
				
				// calculate demand
				$demand = 0.0;
				if( $category['required'] > 0 )
					$demand = 1.0 - ($stock['overall'] / $category['required']);
				
				// select images
				$img1 = "gray";
				$img2 = "gray";
				if( $demand > 0.80 ){
					$img1 = "red";
					$img2 = "red";
				} else if( $demand > 0.65 ){
					$img2 = "red";
				} else if( $demand > 0.50 ){
					$img1 = "yellow";
					$img2 = "yellow";
				} else if( $demand > 0.40 ){
					$img2 = "yellow";
				} else if( $demand > 0.25 ){
					$img1 = "green";
					$img2 = "green";
				}  else if( $demand > 0.10 ){
					$img2 = "green";
				}
				
				
				
				// check for root element
				if( $hierarchyEntry['level'] == 0 ){
					
					// check if to close previous table
					if( !$table_closed ) print "</table><p></p>";
					
					// print header
					print "<table>";
					$highlight = true;
					$table_closed = false;
				}
				
				// print line start
				print "<tr class='".($highlight ? "highlight" : "")."'>"
					."<td class='centertext'><img src='img/star-".$img1.".png' /><img src='img/star-".$img2.".png' /></td>"
					."<td class='".($hierarchyEntry['level'] == 0 ? "text_bold" : "")." td_max'>";
				
				// print category name
				print str_repeat( '&#160;&#160;&#160;&#160;', $hierarchyEntry['level'] )
					.$hierarchyEntry['hierarchy'][0]['name']
					." (". $stock['overall'] ." ". getUnit($category) ." ". LANG('in_stock') .")"
					."</td>";
					
				// print details button and line end
				if( isset($_SESSION['warehouseinfo']) ){
					print "<td>"
						."<a href='javascript: showDemandStock(".$categoryId.")' class='button smallbutton yellow'>"
						.LANG('details')."</a>"
						."</td>";
				}
				
				print "</tr>";
				
				// print details container start
				print "<tr id='stock_info_".$categoryId."' class='hidetext " .($highlight ? "highlight" : ""). "'><td></td>"
					."<td id='stock_details_".$categoryId."' class='td_max'>"
					."<table class='table hidetext tinytext' id='stock_details_table_".$categoryId."'>"
						."<tr id='stock_details_overview_".$categoryId."'></tr>"
						."<tr id='stock_details_unlocated_loose_".$categoryId."'></tr>"
						."<tr id='stock_details_unlocated_palette_".$categoryId."'></tr>"
						."<tr id='stock_details_located".$categoryId."'></tr>"
					."</table>"
						
					."<div align='center' id='stock_loading_".$categoryId."'><img src='img/loading.gif' /></div>"
					."</td>";
				
				// print details container end
				print "<td></td></tr>";
				
				// invert highlight flag
				$highlight = !$highlight;
				
			}
			
			// check if to close previous table
			if( !$table_closed ) print "</table><p></p>";
			
		}
	?>
	</div>
	
</div>
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
	</div>

	<div class="groupitem">
	<?php 
		if( isset($_GET['demand']) ){
			
			// get sorted category hierarchy
			$categories = db_getCategories( $warehouse['id'], "NULL", "NULL" );
			$hierarchies = getCategoryHierarchyStrings( $warehouse['id'], $categories );
			array_multisort( $hierarchies, SORT_ASC );
			$table_closed = true;
			$highlight = true;
			
			foreach( $hierarchies as $hierarchy ){
				$level = substr_count( $hierarchy['hierarchy'], '>' );
				$categoryId = $hierarchy['id'];
				$category = db_getCategory( $warehouse['id'], $categoryId );
				$hasChild = db_hasChildCategory( $warehouse['id'], $categoryId );
				$stock = db_getCategoryStockInfo( $categoryId, "NULL", "NULL");
				
				if( count($stock) > 0 && count($category) > 0 ){
					$stock = $stock[0];
					$category = $category[0];
				
					// calculate demand
					$demand = 0.0;
					if( $category['required'] > 0 )
						$demand = 1.0 - ($stock['total'] / $category['required']);
					
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
					
					// check if element is root
					if( $level == 0 ){
						if( !$table_closed )
							print "</table><p></p>";
						print "<table>";
						$table_closed = false;
					}
					
					// print category info
					print "<tr>";
					print "<td><img src='img/star-".$img1.".png' /><img src='img/star-".$img2.".png' /></td>";
					print "<td class='td_max".($highlight ? " highlight" : "")."'>".($level == 0 ? "<b>" : "").$hierarchy['hierarchy'].($level == 0 ? "</b>" : "");
					print "</td>";
					print "<td class='".($highlight ? "highlight" : "")."'>".( !$hasChild ? "<a href='javascript: showDemandStock(".$categoryId.")' class='button smallbutton yellow'>".LANG('details') : "" )."</a></td>";
					print "</tr>";
					
					$highlight = !$highlight;
					
					// print stock info
					if( !$hasChild ){
						
						print "<tr id='stock_info_".$categoryId."' class='hidetext'><td></td><td class='tr_max, tinytext'>";
						print LANG('income')." = ".($stock['income_total'] ? $stock['income_total'] : "0").LANG('pieces_short')." ";
						print LANG('outgo')." = ".($stock['outgo_total'] ? $stock['outgo_total'] : "0").LANG('pieces_short')."<br />";
						print "</td></tr>";
					}
					
				}
			}
			
			print "</table></div>";
			
		}
	?>
	</div>
	
</div>
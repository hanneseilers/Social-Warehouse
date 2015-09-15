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
			$hierarchies = getCategoryHierarchyStrings( $warehouse['id'], $categories );
			array_multisort( $hierarchies, SORT_ASC );
			$table_closed = true;
			$highlight = true;
			
			foreach( $hierarchies as $hierarchy ){
				$level = substr_count( $hierarchy['hierarchy'], '>' );
				$categoryId = $hierarchy['id'];
				$category = db_getCategory( $warehouse['id'], $categoryId );
				$hasChild = db_hasChildCategory( $warehouse['id'], $categoryId );
				$stock = getRecursiveStockInfo( $warehouse['id'], $categoryId );
				
				if( count($category) > 0 ){
					$category = $category[0];
				
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
					print "<td class='td_max".($highlight ? " highlight" : "")."'>".($level == 0 ? "<b>" : "").$hierarchy['hierarchy'].($level == 0 ? "</b> " : " ");
					print " <span class=' tinytext'>".$stock['overall'].LANG('pieces_short');
					print "</span></td>";
					print "<td class='".($highlight ? "highlight" : "")."'><a href='javascript: showDemandStock(".$categoryId.")' class='button smallbutton yellow'>"
								.LANG('details')."</a></td>";
					print "</tr>";
					
										
					
					// print stock info					
					print "<tr id='stock_info_".$categoryId."' class='hidetext'><td></td><td colspan=2 class='tr_max tinytext".($highlight ? " highlight" : "")."'>"
						."<table class='tinytext'><tr>";
					print "<td>".LANG('total')." =</td>"
						."<td><img src='img/male_s.png' />".$stock['total']['male'].LANG('pieces_short')."</td>"
						."<td><img src='img/female_s.png' />".$stock['total']['female'].LANG('pieces_short')."</td>"
						."<td><img src='img/baby_s.png' />".$stock['total']['baby'].LANG('pieces_short')."</td>"
						."<td><img src='img/unisex_s.png' />".$stock['total']['unisex'].LANG('pieces_short')."</td>"
						."<td><img src='img/asex_s.png' />".$stock['total']['asex'].LANG('pieces_short')."</td></tr>";
				
					if( isset($_SESSION['warehouseinfo']) ){
						print "<tr>"
							. "<td>".LANG('income')." =</td>"
							."<td><img src='img/male_s.png' />".$stock['income']['male'].LANG('pieces_short')."</td>"
							."<td><img src='img/female_s.png' />".$stock['income']['female'].LANG('pieces_short')."</td>"
							."<td><img src='img/baby_s.png' />".$stock['income']['baby'].LANG('pieces_short')."</td>"
							."<td><img src='img/unisex_s.png' />".$stock['income']['unisex'].LANG('pieces_short')."</td>"
							."<td><img src='img/asex_s.png' />".$stock['income']['asex'].LANG('pieces_short')."</td></tr>";
						print "<tr>"
							."<td>".LANG('outgo')." = "
							."<td><img src='img/male_s.png' />".$stock['outgo']['male'].LANG('pieces_short')."</td>"
							."<td><img src='img/female_s.png' />".$stock['outgo']['female'].LANG('pieces_short')."</td>"
							."<td><img src='img/baby_s.png' />".$stock['outgo']['baby'].LANG('pieces_short')."</td>"
							."<td><img src='img/unisex_s.png' />".$stock['outgo']['unisex'].LANG('pieces_short')."</td>"
							."<td><img src='img/asex_s.png' />".$stock['outgo']['asex'].LANG('pieces_short')."</td></tr></table>";
						
						if( !$hasChild ){
							// add palette info
							print "<p></p>";
							
							// add unlocated stock
							$looseStock_unlocated = db_getStockInfo( $categoryId, "NULL", "NULL" );
							
							// add loose unlocated stock
							print "<b>Not located:</b><br />";
							print "<table class='tinytext'><tr>";
							print "<td>".LANG('loose_stock')." =</td>"
								."<td><img src='img/male_s.png' />".$looseStock_unlocated['male']['total'].LANG('pieces_short')."</td>"
								."<td><img src='img/female_s.png' />".$looseStock_unlocated['female']['total'].LANG('pieces_short')."</td>"
								."<td><img src='img/baby_s.png' />".$looseStock_unlocated['baby']['total'].LANG('pieces_short')."</td>"
								."<td><img src='img/unisex_s.png' />".$looseStock_unlocated['unisex']['total'].LANG('pieces_short')."</td>"
								."<td><img src='img/asex_s.png' />".$looseStock_unlocated['asex']['total'].LANG('pieces_short')."</td></tr>";
							print "</tr></table><br />";
							
							// add located stock
							$locations = db_getLocations( $warehouse['id'] );
							foreach( $locations as $location ){
								
								$palettes = db_getPalettesAtLocation( $categoryId, $location['id'] );
								$looseStock = db_getStockInfo( $categoryId, $location['id'], "NULL" );
								
								// ad location name
								if( count($palettes) > 0
										|| $looseStock['male']['total'] > 0
										|| $looseStock['female']['total'] > 0
										|| $looseStock['baby']['total'] > 0
										|| $looseStock['unisex']['total'] > 0
										|| $looseStock['asex']['total'] > 0){
									print "<b>".$location['name'].":</b><br />";
								
									// add loose stock
									print "<table class='tinytext'><tr>";
									print "<td>".LANG('loose_stock')." =</td>"
										."<td><img src='img/male_s.png' />".$looseStock['male']['total'].LANG('pieces_short')."</td>"
										."<td><img src='img/female_s.png' />".$looseStock['female']['total'].LANG('pieces_short')."</td>"
										."<td><img src='img/baby_s.png' />".$looseStock['baby']['total'].LANG('pieces_short')."</td>"
										."<td><img src='img/unisex_s.png' />".$looseStock['unisex']['total'].LANG('pieces_short')."</td>"
										."<td><img src='img/asex_s.png' />".$looseStock['asex']['total'].LANG('pieces_short')."</td></tr>";
									print "</tr></table><br />";
								}
									
								// add palettes
								if( count($palettes) > 0 ){
									print "<table class='tinytext'>";
									foreach( $palettes as $palette ){
										$paletteStock = db_getStockInfo( $categoryId, $location['id'], $palette['id'] );
										print "<tr>";
										print "<td>#".$palette['name']." =</td>"
											."<td><img src='img/male_s.png' />".$paletteStock['male']['total'].LANG('pieces_short')."</td>"
											."<td><img src='img/female_s.png' />".$paletteStock['female']['total'].LANG('pieces_short')."</td>"
											."<td><img src='img/baby_s.png' />".$paletteStock['baby']['total'].LANG('pieces_short')."</td>"
											."<td><img src='img/unisex_s.png' />".$paletteStock['unisex']['total'].LANG('pieces_short')."</td>"
											."<td><img src='img/asex_s.png' />".$paletteStock['asex']['total'].LANG('pieces_short')."</td></tr>";
										print "</tr>";
									}
									print "</table>";
								}									
								
								print "<p></p>";
							}
							
						}
					} else {
						print "</table>";
					}
					print "</td></tr>";					
					
					$highlight = !$highlight;
				}
			}
			
			print "</table></div>";
			
		}
	?>
	</div>
	
</div>
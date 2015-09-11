<div class="warehouseinfo">
	<?php		
		$name = $_SESSION['warehouseinfo']['name'];
		$id = $_SESSION['warehouseinfo']['id'];
		$country = $_SESSION['warehouseinfo']['country'];
		$city = $_SESSION['warehouseinfo']['city'];
		
		print "<div class=\"table\"><span class=\"warehousename\">Warehouse: ".$name."</span>";
		print "<a href=\"javascript: changeWarehouseInfo();\" class=\"button button_table_cell green\">Edit</a> ";
		print "<a href=\"?demand=".$id."\" class=\"button button_table_cell yellow\">Demand</a>";
		print "<a href=\"javascript: logout();\" class=\"button button_table_cell red\">Logout</a></div>";
		
	?>
	<table class="edit" id="warehouseeditdata">
	<tr>
		<td>Warehouse name:</td>
		<td><?php print "<input type=\"text\" value=\"".$name."\" id=\"warehousenamenew\" />"; ?></td>
	</tr>
	<tr>
		<td>Country:</td>
		<td>
			<select id="country">
				<?php
					print getCountryDropdownOptions( "countries/countries", $country );
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td>City:</td>
		<td colspan="3">
			<?php 
				print "<input type=\"text\" id=\"city\" value=\"".$city."\" />";
			?>
		</td>
		<td id="citymissing" class="errortext hidetext">City is missing!</td>
	</tr>
	<tr>
		<td><br /></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input type="password" id="password" /></td>
		<td>Repeat password:</td>
		<td><input type="password" id="password-repeat" /></td>
		<td id="passwordwrong" class="errortext hidetext">Passwords aren't the same!</td>
	</tr>
	<tr>
		<td colspan="4"><textarea rows="3" id="warehousedescription"><?php print $_SESSION['warehouseinfo']['description']; ?></textarea></td>
	</tr>
	<tr>
		<td align="center"><a href="javascript: deleteWarehouse();" class="button button_block red">Delete Warehouse</a></td>
	</tr>
	</table>
	
</div>
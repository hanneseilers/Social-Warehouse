<div class="warehouseinfo">
	<?php		
		$name = $_SESSION['warehouseinfo']['name'];
		$id = $_SESSION['warehouseinfo']['id'];
		
		print "<div class=\"table\"><span class=\"warehousename\">Warehouse: ".$name."</span>";
		print "<span class=\"edit\"><a href=\"javascript: changeWarehouseInfo();\" class=\"button loginbutton\">Edit</a> ";
		print "<a href=\"javascript: logout();\" class=\"button logoutbutton\">Logout</a></span></div>";
		
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
					include( "countries/countries.php" );
					print getCountryDropdownOptions( "countries/countries" );
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td>City:</td>
		<td><input type="text" id="city" /></td>
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
		<td align="center"><a href="javascript: deleteWarehouse();" class="button button_block logoutbutton">Delete Warehouse</a></td>
	</tr>
	</table>
	
</div>
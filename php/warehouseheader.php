<div class="groupitem">

	<div>
	<?php		
		$name = $_SESSION['warehouseinfo']['name'];
		$id = $_SESSION['warehouseinfo']['id'];
		$country = $_SESSION['warehouseinfo']['country'];
		$city = $_SESSION['warehouseinfo']['city'];
		
		print "<span class='group_left text_bold'>".LANG('warehouse').": ".$name."</span>";
		print "<span class='inline_text hidetext errortext' id='warehouse_name_error'>".LANG('warehouse_name_error')."</span>";
		print "<span class='inline_text hidetext errortext' id='warehouse_name_missing'>".LANG('warehouse_name_missing')."</span>";
		print "<a href='javascript: editWarehouse();' class='button green'>".LANG('edit')."</a> ";
		print "<a href='?demand=".$id."' class='button yellow' target='_blanc'>".LANG('stock_details')."</a>";
		print "<a href='javascript: logout();' class='button red'>".LANG('logout')."</a>";
		
	?>
	</div>
	
	<table class="hidetext" id="warehouseeditdata">
	<tr>
		<td><?php print LANG('warehouse_name'); ?>:</td>
		<td><?php print "<input type='text' value='".$name."' id='warehousenamenew' />"; ?></td>
	</tr>
	<tr>
		<td><?php print LANG('country'); ?>:</td>
		<td>
			<select id="country">
				<?php
					print getCountryDropdownOptions( "countries/countries", $country );
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?php print LANG('city'); ?>:</td>
		<td colspan="3">
			<?php 
				print "<input type='text' id='city' value='".$city."' />";
			?>
		</td>
		<td id="citymissing" class="errortext hidetext"><?php print LANG('city_missing'); ?></td>
	</tr>
	<tr>
		<td><br /></td>
	</tr>
	<tr>
		<td><?php print LANG('password'); ?>:</td>
		<td><input type="password" id="password" /></td>
		<td><?php print LANG('password_repeat'); ?>:</td>
		<td><input type="password" id="password-repeat" /></td>
		<td id="passwordwrong" class="errortext hidetext"><?php print LANG('passwords_not_equal'); ?></td>
	</tr>
	<tr>
		<td colspan="4"><textarea rows="3" id="warehousedescription"><?php print $_SESSION['warehouseinfo']['description']; ?></textarea></td>
	</tr>
	<tr>
		<td align="center"><a href="javascript: deleteWarehouse();" class="button block red"><?php print LANG('delete_warehouse'); ?></a></td>
	</tr>
	</table>
</div>
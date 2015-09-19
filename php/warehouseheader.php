<div class="groupitem" id="warehouseheader">

	<div>
	<?php		
		$name = $_SESSION['warehouseinfo']['name'];
		$id = $_SESSION['warehouseinfo']['id'];
		$mail = $_SESSION['warehouseinfo']['mail'];
		$country = $_SESSION['warehouseinfo']['country'];
		$city = $_SESSION['warehouseinfo']['city'];
		
		$disableLocationLess = $_SESSION['warehouseinfo']['disableLocationLess'];
		$disablePaletteLess = $_SESSION['warehouseinfo']['disablePaletteLess'];
		
		print "<span class='group_left text_bold'>".LANG('warehouse').": ".$name."</span>";
		print "<span class='inline_text hidetext errortext' id='warehouse_name_error'>".LANG('warehouse_name_error')."</span>";
		print "<span class='inline_text hidetext errortext' id='warehouse_name_missing'>".LANG('warehouse_name_missing')."</span>";
		
		if( !isset($_SESSION['warehouseinfo']['restricted']) || !$_SESSION['warehouseinfo']['restricted'] ){
			print "<a href='javascript: editWarehouse();' id='warehouse_edit' class='button green'>".LANG('edit')."</a> ";
		}
		
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
		<td>
			<?php 
				print "<input type='text' id='city' value='".$city."' />";
			?>
		</td>
		<td id="citymissing" class="errortext hidetext"><?php print LANG('city_missing'); ?></td>
	</tr>
	<tr>
		<td><?php print LANG('e-mail'); ?></td>
		<td>
			<?php
				print "<input type='email' id='mail' value='".$mail."' />";
			?>
		</td>
		<td id="emailwrong" class="errortext hidetext"><?php print LANG('email_error'); ?></td>
	</tr>
	<tr>
		<td><?php print LANG('password'); ?>:</td>
		<td><input type="password" id="password" /></td>
		<td><?php print LANG('password_repeat'); ?>:</td>
		<td><input type="password" id="password-repeat" /></td>
		<td id="passwordwrong" class="errortext hidetext"><?php print LANG('passwords_not_equal'); ?></td>
	</tr>	
	<tr>
		<td colspan=4><?php print LANG('description'); ?>:</td>
	</tr>
	<tr>
		<td colspan=4><textarea rows="3" id="warehousedescription"><?php print $_SESSION['warehouseinfo']['description']; ?></textarea></td>
	</tr>
	
	<tr>
		<td><br /></td>
	</tr>
	
	<tr>
		<td colspan=2>
			<?php
				if( $disableLocationLess )
					print "<input type='checkbox' id='disableLotionLess' checked>";
				else
					print "<input type='checkbox' id='disableLotionLess'>";
				print LANG('disable_location_less');				
			?>
		</td>
		<td colspan=2>
			<br />
			<?php				
				if( $disablePaletteLess )
					print "<input type='checkbox' id='disablePaletteLess' checked>";
				else
					print "<input type='checkbox' id='disablePaletteLess'>";
				print LANG('disable_palette_less');
			?>
		</td>
	</tr>
	
	<tr>
		<td colspan=4>
			<a href="javascript: deleteWarehouse();" class="button block red"><?php print LANG('delete_warehouse'); ?></a>
			<a href="javascript: setRestricted();" class="button block yellow"><?php print LANG('restrict_permissions'); ?></a>
		</td>
	</tr>
	</table>
</div>
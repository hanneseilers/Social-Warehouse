<div class="register">
	<h1><?php print LANG('new_warehouse'); ?></h1>
	<table>
		<tr>
			<td><?php print LANG('warehouse_name'); ?>:</td>
			<td><input type="text" id="warehousename" /></td>
			<td id="warehousewrong" class="errortext hidetext"><?php print LANG('warehouse_name_error'); ?></td>
			<td id="warehousemissing" class="errortext hidetext"><?php print LANG('warehouse_name_missing'); ?></td>
		</tr>
		<tr>
			<td><?php print LANG('country'); ?>:</td>
			<td>
				<select id="country">
					<?php
						print getCountryDropdownOptions( "countries/countries" );
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php print LANG('city'); ?>:</td>
			<td><input type="text" id="city" /></td>
			<td id="citymissing" class="errortext hidetext"><?php print LANG('city_missing'); ?></td>
		</tr>
		<tr>
			<td><br /></td>
		</tr>
		<tr>
			<td><?php print LANG('password'); ?>:</td>
			<td><input type="password" id="password" /></td>
			<td id="passwordwrong" class="errortext hidetext"><?php print LANG('passwords_not_equal'); ?></td>
			<td id="passwordmissing" class="errortext hidetext"><?php print LANG('password_missing'); ?></td>
		</tr>
		<tr>
			<td><?php print LANG('password_repeat'); ?>:</td>
			<td><input type="password" id="password-repeat" /></td>
		</tr>
		<tr>
			<td><?php print LANG('description'); ?> (<?php print LANG('optional'); ?>):</td>
			<td><textarea rows="5" id="warehousedescription"></textarea>
		</tr>
		<tr>
			<td colspan="2" align="center"><a href="javascript: addWarehouse();" class="button block"><?php print LANG('add_warehouse'); ?></a></td>
		</tr>
	</table>
</div>
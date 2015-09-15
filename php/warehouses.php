<h1><?php print LANG('warehouses'); ?></h1>
<!--
Country: <select id="filtercountry" onchange="filterCountry();">
	<option>-All-</option>
	<?php
		print getCountryDropdownOptions( "countries/countries" );
	?>
</select>
City: <input type="text" id="filtercity" onkeypress="filterCity();" />
-->

<div class="warehouseslist">
	<?php
		
		// list warehouses
		$warehouses = db_getWarehouses();
		foreach( $warehouses as $vWarehouse ){			
			print "<div class='groupitem'>";
			print "<span class='group_left text_bold' onmousemove='moveWarehouseDescription(event);' onmouseover='showWarehouseDescription(".$vWarehouse['id'].");', onmouseout='hideWarehouseDescription();'>";
			print "<span id='country_".$vWarehouse['country']."'><img src='countries/flags/".getCountryCode("countries/countries", $vWarehouse['country']).".png' /></span> ";
			print "<span id='city_".$vWarehouse['city']."'>".$vWarehouse['city']."</span> : ";
			print $vWarehouse['name']."</span>";
			print "<span class='inline_text hidetext errortext' id='warehouseloginfailed".$vWarehouse['id']."'>".LANG('password_wrong')."</span>";
			print "<span class='inline_text hidetext'>".LANG('password').": ";
			print "<input type='password' id='warehousepw".$vWarehouse['id']."' onkeypress='if(event.keyCode == 13) login(".$vWarehouse['id'].");' /></span>";
			print "<img src='img/loading.gif' class='loadinggif' id='warehouseload".$vWarehouse['id']."' />";
			print "<a href='?demand=".$vWarehouse['id']."' class='button yellow' id='warehousedemand".$vWarehouse['id']."'>".LANG('demand')."</a>";
			print "<a href='javascript: login(".$vWarehouse['id'].");' class='button green' id='warehouselogin".$vWarehouse['id']."'>".LANG('login')."</a></div>";
		}
		
	?>
</div>
<div class="description_overlay" id="overlay">
	<img src="img/loading.gif" id="overlay_loading" />
	<span class="hidetext" id="overlay_text"></span>
</div>
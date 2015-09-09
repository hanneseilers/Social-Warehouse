<h1>Warehouses</h1>
<div class="warehouseslist">
	<?php
		
		// list warehouses
		foreach( db_getWarehouses() as $vWarehouse ){
			print "<div class=\"warehouseitem\">";
			print "<span class=\"warehousename\" onmousemove=\"moveWarehouseDescription(event);\" onmouseover=\"showWarehouseDescription(".$vWarehouse['id'].");\", onmouseout=\"hideWarehouseDescription();\">";
			print "<span id=\"country_".$vWarehouse['country']."\"><img src=\"countries/flags/".getCountryCode("countries/countries", $vWarehouse['country']).".png\" /></span> ";
			print "<span id=\"city_".$vWarehouse['city']."\">".$vWarehouse['city']."</span> : ";
			print $vWarehouse['name']."</span>";
			print "<span class=\"loginfailed\" id=\"warehouseloginfailed".$vWarehouse['id']."\">Passwort falsch!</span>";
			print "<span class=\"loginpw\">Password: <input type=\"password\" id=\"warehousepw".$vWarehouse['id']."\" onkeypress=\"if(event.keyCode == 13) login(".$vWarehouse['id'].");\" /></span>";
			print "<img src=\"img/loading.gif\" class=\"loginloading\" id=\"warehouseload".$vWarehouse['id']."\" />";
			print "<span class=\"edit\"><a href=\"?demand=".$vWarehouse['id']."\" class=\"button yellowbutton\" id=\"warehousereq".$vWarehouse['id']."\">Demand</a>";
			print "<a href=\"javascript: login(".$vWarehouse['id'].");\" class=\"button loginbutton\" id=\"warehouselogin".$vWarehouse['id']."\">Login</a></span></div>";
		}
	
	?>
</div>
<div class="warehousedescription" id="warehousedescriptionoverlay">
	<img src="img/loading.gif" id="descriptionloading" />
	<span id="warehousedescriptiontext" class="hidetext">test</span>
</div>
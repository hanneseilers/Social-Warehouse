<h1>Gruppen</h1>
<div class="groupslist">
	<?php
		
		// list groups
		foreach( db_getGroups() as $vGroup ){
			print "<div class=\"groupitem\"><span class=\"groupname\">".$vGroup['name']."</span>";
			print "<a href=\"\" class=\"loginbutton\">Login</a></div>";
		}
	
	?>
</div>
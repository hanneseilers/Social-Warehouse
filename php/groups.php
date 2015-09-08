<h1>Gruppen</h1>
<div class="groupslist">
	<?php
		
		// list groups
		foreach( db_getGroups() as $vGroup ){
			print "<div class=\"groupitem\"><span class=\"groupname\">".$vGroup['name']."</span>";
			print "<span class=\"loginfailed\" id=\"grouploginfailed".$vGroup['id']."\">login failed</span>";
			print "<span class=\"loginpw\">Passwort: <input type=\"password\" id=\"grouppw".$vGroup['id']."\" /></span>";
			print "<img src=\"img/loading.gif\" class=\"loginloading\" id=\"groupload".$vGroup['id']."\" />";
			print "<a href=\"javascript: login(".$vGroup['id'].");\" class=\"loginbutton\" id=\"grouplogin".$vGroup['id']."\">Login</a></div>";
		}
	
	?>
</div>
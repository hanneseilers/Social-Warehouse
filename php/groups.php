<h1>Gruppen</h1>
<div class="groupslist">
	<?php
		
		// list groups
		foreach( db_getGroups() as $vGroup ){
			print "<div class=\"groupitem\">";
			print "<span class=\"groupname\" onmousemove=\"moveGroupDescription(event);\" onmouseover=\"showGroupDescription(".$vGroup['id'].");\", onmouseout=\"hideGroupDescription();\">";
			print $vGroup['name']."</span>";
			print "<span class=\"loginfailed\" id=\"grouploginfailed".$vGroup['id']."\">Passwort falsch!</span>";
			print "<span class=\"loginpw\">Passwort: <input type=\"password\" id=\"grouppw".$vGroup['id']."\" onkeypress=\"if(event.keyCode == 13) login(".$vGroup['id'].");\" /></span>";
			print "<img src=\"img/loading.gif\" class=\"loginloading\" id=\"groupload".$vGroup['id']."\" />";
			print "<span class=\"edit\"><a href=\"?requirements=".$vGroup['id']."\" class=\"button yellowbutton\" id=\"groupreq".$vGroup['id']."\">Bedarf</a>";
			print "<a href=\"javascript: login(".$vGroup['id'].");\" class=\"button loginbutton\" id=\"grouplogin".$vGroup['id']."\">Login</a></span></div>";
		}
	
	?>
</div>
<div class="groupdescription" id="groupdescriptionoverlay">
	<img src="img/loading.gif" id="descriptionloading" />
	<span id="groupdescriptiontext" class="hidetext">test</span>
</div>
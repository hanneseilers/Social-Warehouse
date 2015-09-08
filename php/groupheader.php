<div class="groupinfo">
	<?php		
		$name = $_SESSION['groupinfo']['name'];
		$id = $_SESSION['groupinfo']['id'];
		
		print "<div class=\"table\"><span class=\"groupname\">Gruppe ".$name."</span>";
		print "<span class=\"edit\"><a href=\"javascript: changeGroupName();\" class=\"loginbutton\">&Auml;ndern</a> ";
		print "<a href=\"javascript: logout();\" class=\"logoutbutton\">Ausloggen</a></span></div>";
		
	?>
	<table class="edit">
	<tr>
		<td>Gruppenname:</td>
		<td><?php print "<input type=\"text\" value=\"".$name."\" id=\"groupnamenew\" />"; ?></td>
	</tr>
	<tr>
		<td>Passwort:</td>
		<td><input type="password" id="password" /></td>
		<td>Passwort wiederholen:</td>
		<td><input type="password" id="password-repeat" /></td>
	</tr>
	</table>
	
</div>
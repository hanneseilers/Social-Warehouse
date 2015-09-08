<div class="groupinfo">
	<?php		
		$name = $_SESSION['groupinfo']['name'];
		$id = $_SESSION['groupinfo']['id'];
		
		print "<div class=\"table\"><span class=\"groupname\">Gruppe ".$name."</span>";
		print "<span class=\"edit\"><a href=\"javascript: changeGroupInfo();\" class=\"loginbutton\">&Auml;ndern</a> ";
		print "<a href=\"javascript: logout();\" class=\"logoutbutton\">Ausloggen</a></span></div>";
		
	?>
	<table class="edit" id="groupeditdata">
	<tr>
		<td>Gruppenname:</td>
		<td><?php print "<input type=\"text\" value=\"".$name."\" id=\"groupnamenew\" />"; ?></td>
	</tr>
	<tr>
		<td>Passwort:</td>
		<td><input type="password" id="password" /></td>
		<td>Passwort wiederholen:</td>
		<td><input type="password" id="password-repeat" /></td>
		<td id="passwordwrong" class="errortext">Passw&ouml;rter stimmen nicht überein!</td>
	</tr>
	<tr>
		<td colspan="4"><textarea rows="3" cols="80" id="groupdescription"><?php print $_SESSION['groupinfo']['description']; ?></textarea></td>
	</tr>
	</table>
	
</div>
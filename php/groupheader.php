<div class="groupinfo">
	<?php		
		$name = $_SESSION['groupinfo']['name'];
		$id = $_SESSION['groupinfo']['id'];
		
		print "<div class=\"table\"><span class=\"groupname\">Warehouse: ".$name."</span>";
		print "<span class=\"edit\"><a href=\"javascript: changeGroupInfo();\" class=\"button loginbutton\">Edit</a> ";
		print "<a href=\"javascript: logout();\" class=\"button logoutbutton\">Logout</a></span></div>";
		
	?>
	<table class="edit" id="groupeditdata">
	<tr>
		<td>Warehouse name:</td>
		<td><?php print "<input type=\"text\" value=\"".$name."\" id=\"groupnamenew\" />"; ?></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input type="password" id="password" /></td>
		<td>Repeat password:</td>
		<td><input type="password" id="password-repeat" /></td>
		<td id="passwordwrong" class="errortext hidetext">Passwords aren't the same!</td>
	</tr>
	<tr>
		<td colspan="4"><textarea rows="3" id="groupdescription"><?php print $_SESSION['groupinfo']['description']; ?></textarea></td>
	</tr>
	<tr>
		<td align="center"><a href="javascript: deleteGroup();" class="button button_block logoutbutton">Delete Group</a></td>
	</tr>
	</table>
	
</div>
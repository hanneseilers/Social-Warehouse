<div class="register">
	<h1>Neue Gruppe anlegen</h1>
	<table>
		<tr>
			<td>Gruppenname:</td>
			<td><input type="text" id="groupname" /></td>
			<td id="groupwrong" class="errortext hidetext">Gruppenname bereits vergeben!</td>
		</tr>
		<tr>
			<td>Passwort:</td>
			<td><input type="password" id="password" /></td>
			<td id="passwordwrong" class="errortext hidetext">Passw&ouml;rter stimmen nicht überein!</td>
			<td id="passwordmissing" class="errortext hidetext">Kein Passwort vergeben!</td>
		</tr>
		<tr>
			<td>Passwort wiederholen:</td>
			<td><input type="password" id="password-repeat" /></td>
		</tr>
		<tr>
			<td>Beschreibung (optional):</td>
			<td><textarea rows="5" id="groupdescription"></textarea>
		</tr>
		<tr>
			<td colspan="2" align="center"><a href="javascript: addGroup();" class="button button_block">Gruppe anlegen</a></td>
		</tr>
	</table>
</div>
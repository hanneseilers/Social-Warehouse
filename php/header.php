<div class="header">Spendenverwaltung</div>
<div class="breadcrumps">
	<a href="">
	<?php 
	
		// Show root
		if( isset($_SESSION['groupinfo']) )
			print $_SESSION['groupinfo']['name'];
		else
			print "Home";
	?>
	</a>
	
	<?php
	
		// show current catgeory
		if( isset($_SESSION['curCategory']) ){
			print ">";
		}
	
	?>
	
</div>
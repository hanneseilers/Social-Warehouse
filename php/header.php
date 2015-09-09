<div class="header"><img src="img/logo-48.png" /><span>Social Warehouse</span></div>
<div class="breadcrumps">
	
	<?php 
	
		// Show root
		if( isset($_SESSION['groupinfo']) )
			print "<a href=\"\">".$_SESSION['groupinfo']['name']."</a>";
		else
			print "<a href=\"?\">Home</a>";
	?>
	</a>
	
	<?php
	
		// show current catgeory
		if( isset($_GET['requirements']) ){
			print " > <a href=\"\">Warehouse Demand</a>";
		}
		else if( isset($_SESSION['curCategory']) ){
			print " > ";
		}
	
	?>
	
</div>
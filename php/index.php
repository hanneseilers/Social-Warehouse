<html>
<head rofile="http://www.w3.org/2005/10/profile">
<title>Social Warehouse</title>

<link rel="icon" 
      type="image/png" 
      href="favicon.png">

<link type="text/css" rel="stylesheet" href="style.css">
<link rel="stylesheet" href="js/extern/jstree/themes/default/style.min.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.scrollto/2.1.2/jquery.scrollTo.min.js"></script> <!--  Source: https://github.com/flesler/jquery.scrollTo -->
<script src="js/extern/jstree/jstree.min.js"></script> <!-- Source: https://github.com/vakata/jstree -->
<script src="js/extern/jspdf.min.js"></script> <!-- Source: https://github.com/MrRio/jsPDF -->
<script src="js/extern/cookie.js"></script> <!-- Source: https://github.com/js-cookie/js-cookie -->
<script src="js/extern/md5.js"></script> <!-- Source: http://aktuell.de.selfhtml.org/artikel/javascript/md5/ -->
<script src="js/extern/base64.js"></script> <!-- Source: http://phpjs.org/functions/base64_encode/ -->
<script src="js/extern/cssStyle.js"></script> <!-- Source: http://stackoverflow.com/questions/16965515/how-to-get-a-style-attribute-from-a-css-class-by-javascript-jquery -->
<script src="js/lang.js"></script>
<script src="js/base.js"></script>
<script src="js/classes/Main.js"></script>
<script src="js/classes/Overlay.js"></script>
<script src="js/classes/Warehouse.js"></script>
<script src="js/classes/Session.js"></script>
<script src="js/classes/Location.js"></script>
<script src="js/classes/Palette.js"></script>
<script src="js/classes/Category.js"></script>
<script src="js/classes/Carton.js"></script>
<script src="js/classes/Stock.js"></script>
<script src="js/classes/Barcodescanner.js"></script>

<meta charset="utf-8">
</head>
<body onLoad="javascript: load();">

<div class="status_message hidetext" id="status_message" onclick="javascript: hideStatusMessage();"></div>
<div class="mainframe">
	
	<?php		
		// include multilanguage support
		include( "lang/lang.php" );
	?>
	
	<div class="header table">
		<span class="group_left"><img src="img/logo-48.png" /> Social Warehouse</span>
		<a href="lang/<?php print HELP_FILE(); ?>" target="_blanc" class="button table_cell smalltext lightgray"><img src="img/help.png" />&nbsp;<?php print LANG('help'); ?></a>
	</div>
	
	<div></div>
	<div id="menu"></div>
	<div id="loading" class="centertext tinytext"><img src="img/loading.gif" /> <?php print LANG('loading'); ?></div>
	<div id="content"></div>
	
	<div class="footer">
		Social Warehouse, published under <a href="http://www.gnu.org/licenses/gpl-3.0.txt" target="_blanc">GPLv3</a> by <a href="http://www.hanneseilers.de">Hannes Eilers</a>
	</div>
</div>

<div id="overlay" class="overlay">
	<div class="overlay_main">
		<div id="overlay_content"></div>
		<div class="hspacer"></div>
		<div id="overlay_buttons"></div>
	</div>
</div>

<span id='session_maxtime' class='hidetext'>1800</span>
</body>
</html>
<html>
<head rofile="http://www.w3.org/2005/10/profile">
<title>Social Warehouse Help</title>

<link rel="icon" 
      type="image/png" 
      href="favicon.png">

<link type="text/css" rel="stylesheet" href="../../style.css">

<meta charset="utf-8">
</head>
<body>

<?php 
	include( '../lang.php' );
?>

<div class="mainframe help">

	<div class="header table">
		<span class="group_left"><img src="../../img/help.png" /> Social Warehouse Help</span>
	</div>
	
	<div>	
		<h1>Introduction</h1>
		Social Warehouse is an application to manage storages of charity organisations. It is optimized for storing donations like clothes-donations.
		It allows to store items in categories at serveral locations and/or on several palettes. Items can easily be added or removed from any of these storages.
		An overview gives information where to find a specific item and informs visitors and donaters what items you need most,
		while you can define your demand on every specific item.s		
	</div>
	<div>
		<h1>Getting started</h1>
		To start create a new warehouse using the form at the bototm of the main page. Take your organisation name as waerhouse name or whatever you like.
		Select your organisations country and city and enter a password for login and if you like a description.
		As description you can add plain text or html code with a link to your website for example.
		Also if you have different locations for storing your items, you need to register only one warehouse. You can add different locations later.
		<p></p>
		After you logged into your new warehouse you can select between three buttons.
		<ul>
			<li>At <i><?php print LANG('locations'); ?></i>, you can add new locations where you want to store your items, use it for different addresses of your real-world warehouse.</li>
			<li>At <i><?php print LANG('palettes'); ?></i>, you can manage palettes in sense of bundles of items. Often palettes are used to store a lot of items for a longer time. </li>
			<li><i><?php print LANG('stock'); ?></i> let's you manage your categories and offers you a form where you can add or remove items.</li>
		</ul>
		Start by creating different locations, if needed and categories. After that you can start by adding items to your stock.
	</div>
	
	<div>
		<h1>Add/remove items to/from stock</h1>
		You can add or remove items from your stock very easily. There are different scenarios for that.
		<p></p>
		<b>Loose Stock</b><br />
		If you are using only one location and a loose stock without palettes, you can simply select the category where you want to add/remove items to/from.
		Be awaware, that you can only add/remove items from categories, that didn't contain subcategories.
		<p></p>
		Select if the item is for male, female, baby persons (or none of them) and if you estimated the amount of items.
		You can select/deselect any of these options by clicking the corressponding button. Enter the number of items you want to add
		into <i><?php print LANG('income'); ?></i> input field and the number of items you want to remove into <i><?php print LANG('outgo'); ?></i> input field.
		Press the enter key or click <i><?php print LANG('add_to_loose_stock'); ?></i>.
		<p></p>
		You selection for category, male, female, baby, estimated and income or outgo is saved and you can directly enter a new number for your last selected input field, if you want to.<br />
		Select the category name above the form to go up inside the category tree.
		<p></p>
		
		<b>Stock at location and/or palette</b><br />
		You can also select a location and/or palette by clicking on the location and/or palette at the locations/palettes overview.
		Than you're adding your items only at this particular location/palette.
	</div>
	
	<div>
		<h1>Terms of use</h1>
		Charity organisations that are working without profit can use this service for free.<br />
		Because of that this service is maintained by a private person, there are no garantuees or any claims in case of problems with this service.
		The service hoster of this service can delete or edit any stored data set of this service at any time,
		for example to reduce required database space or to delete inactive accounts, without any explanation or notification.
		<p></p>
		You can host your own Social-Warehouse service, if you want, by installing the Social-Warehouse application at your own webserver.
		You will find the code at github: <a href="https://github.com/hanneseilers/Social-Warehouse">https://github.com/hanneseilers/Social-Warehouse</a>.<br />
		Also feel free to submit issues about this application on github to improve this application.
	</div>
	<div>
		<h1></h1>
	</div>

	<div class="footer">
		Spendenverwaltung, published under GPLv2 by <a href="http://www.hanneseilers.de">Hannes Eilers</a>
	</div>
</div>

</body>
</html>
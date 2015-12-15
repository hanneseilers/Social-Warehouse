function showLocations(){
	
	document.getElementById( 'loading' ).style.display = 'block';
	document.getElementById( 'datacontent' ).style.display = 'none';
	
	_tap = 2;
	
	// show form to add location
	var html = "<h1>" + LANG('add_location') + ":</h1>\n"
		+ "<div class='groupitem'><span class='group_left'>"
		+ LANG('location_name') + ": <input type='text' id='addLocation' onkeypress='if(event.keyCode == 13) addLocation();' /></span>"
		+ "<span class='inline_text errortext hidetext' id='location_name_missing'>" + LANG('location_name_missing') + "</span>"
		+ "<span class='inline_text errortext hidetext' id='location_name_error'>" + LANG('location_name_error') + "</span>"
		+ "<a href='javascript: addLocation()' class='button'>" + LANG('add_location') + "</a>" 
		+ "</div>";
	
	html += "<h1 id='scrollTarget'>" + LANG('locations') + ":</h1>";
	html += LANG('location_select_tip');
	html +="<div class='hightlimited' id='locationScrollWindow'>"
	
	// show locations
	for( i=0; i < _locations.length; i++ ){
		html += "\n<div id='location_item_" + _locations[i]['id'] + "'"
				+ " class='groupitem " + (_location == _locations[i]['id'] ? "yellow" : "") + "'><div class='table'>"
			+ "<span class='group_left' onclick='selectLocation(" + _locations[i]['id'] + ", true)'>"
			+ _locations[i]['name'] + "</span>"
			+ "<span class='inline_text hidetext errortext' id='location_name_error_" + _locations[i]['id'] + "'>" + LANG('location_name_error') + "</span>"
			+ "<span class='inline_text hidetext'>" + LANG('location_name') + ": "
			+ "<input type='text' id='editlocation_" + _locations[i]['id'] + "' /></span>"
			+ " <a href='javascript: editLocation(" + _locations[i]['id'] + ")' class='button green'>" + LANG('edit') + "</a>"
			+ " <a href='javascript: showLocationStock(" + _locations[i]['id'] + ")' class='button orange'>" + LANG('details') + "</a>";
		
		if( !_restricted ){
			html += " <a href='javascript: deleteLocation(" + _locations[i]['id'] + ")' class='button red'>" + LANG('delete') + "</a>";
		}
				
		html += "</div><div class='hidetext'><span class='table_cell' id='location_stock_" + _locations[i]['id'] + "' class='tinytext'></span></div>"
			+ "</div>";
	}
	html += "</div>";
	
	// show locations
	showHtml(html);
	
	// scroll to selected location or to scrollTarget
	scrollLocation = document.getElementById( 'location_item_' + _location );
	scrollContainer = document.getElementById( 'locationScrollWindow' );
	if( scrollLocation && scrollContainer ){
		$(window).scrollTo( scrollContainer );
		$(scrollContainer).scrollTo( scrollLocation );
	}
	else
		$.scrollTo( document.getElementById('scrollTarget') );
}

function getLocation(id){
	if( id != null ){
		for( var i=0; i < _locations.length; i++){
			if( _locations[i]['id'] == id ){
				return _locations[i];
			}
		}
	}
	
	return null;
}

function editLocation(id){
	name = new String( document.getElementById( 'editlocation_' + id ).value.trim() );
	
	var vLocation = getLocation(id);	
	if( name.startsWith('%%') ){
		if( vLocation )
			document.getElementById( 'editlocation_' + id ).value = vLocation['name'];
		else
			document.getElementById( 'editlocation_' + id ).value = "";
		return;
	}
	
	document.getElementById( 'location_name_error_' + id ).style.display = "none";
	
	if( name.length > 0 ){
		get( 	{'function': 'editLocation', 'id': id, 'name': base64_encode(name)},
				function(data, status){
					if( status == "success" && data == "ok" ){
						_loadLocations( showLocations );
						document.getElementById( 'editlocation_' + id ).parentElement.style.display = "none";
					} else {
						document.getElementById( 'location_name_error_' + id ).style.display = "table-cell";
					}
		});
	} else if( vLocation ) {
		document.getElementById( 'editlocation_' + id ).value = vLocation['name'];
		document.getElementById( 'editlocation_' + id ).parentElement.style.display = "table-cell";
	}
}

function selectLocation(id, show){
	if( _location == id ){
		_location = null;
	} else {
		_location = id;
	}
	
	if( show )
		showLocations();
}

function addLocation(){
	document.getElementById( 'location_name_missing' ).style.display = "none";
	document.getElementById( 'location_name_error' ).style.display = "none";
	
	var name = new String( document.getElementById( 'addLocation' ).value.trim());
	
	if( name.startsWith('%%') ){
		document.getElementById( 'addLocation' ).value = "";
		return;
	}
	
	if( name.length > 0 ){
		get( 	{'function': 'addLocation', 'name': base64_encode(name)},
				function(data, status){
			if( status == "success" && data == "ok" ){
				_loadLocations( showLocations );
			} else {
				document.getElementById( 'location_name_error' ).style.display = "table-cell";
			}
		});
	} else {
		document.getElementById( 'location_name_missing' ).style.display = "table-cell";
	}
}

function deleteLocation(id){
	get( {'function': 'deleteLocation', 'id': id}, function(){ _loadLocations( showLocations ); });
}

function _loadLocationStockInfo(location){
	get( {'function': 'getLocationStockInfo', 'location': location},
			function(data, status){

				document.getElementById( 'location_stock_' + location ).innerHTML = "";
				if( status == "success" ){
					stock = JSON.parse(data);
					
					// show categories
					if( stock['categories'] != null ){
						document.getElementById( 'location_stock_' + location ).innerHTML =
							document.getElementById( 'location_stock_' + location ).innerHTML
							+ "<b>" + LANG('categories') + ":</b><br />";
						for( var i=0; i < stock['categories'].length; i++ ) {
							var vLocation = stock['categories'][i]
							hierarchy = getCategoryHierrachy( vLocation['category'] )
							document.getElementById( 'location_stock_' + location ).innerHTML =
								document.getElementById( 'location_stock_' + location ).innerHTML
								+ (i != 0 ? "<br />" : "")
								+ hierarchy + " (" + vLocation['total'] + " " + getUnit(vLocation) + ")";							
						}
					}
					
					if( stock['palettes'] != null ){
						document.getElementById( 'location_stock_' + location ).innerHTML =
							document.getElementById( 'location_stock_' + location ).innerHTML
							+ "<div class='hspacer'></div>"
							+ "<b>" + LANG('palettes') + ":</b><br />";
							
						for( var i=0; i < stock['palettes'].length; i++ ) {		
							var vPalette = stock['palettes'][i];
							document.getElementById( 'location_stock_' + location ).innerHTML =
								document.getElementById( 'location_stock_' + location ).innerHTML
								+ (i != 0 ? "<br />#" : "#") + vPalette['name'];							
						}
					}
					
				}
				
			} );
}

function showLocationStock(vLocation){
	if( document.getElementById( 'location_stock_' + vLocation ).parentElement.style.display != "block" ){
		document.getElementById( 'location_stock_' + vLocation ).parentElement.style.display = "block";
		_loadLocationStockInfo( vLocation );
	} else {
		document.getElementById( 'location_stock_' + vLocation ).parentElement.style.display = "none"; 
	}
}

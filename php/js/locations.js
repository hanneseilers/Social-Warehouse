function showLocations($warehouseId){
	setWarehouseId($warehouseId);
	_loadCategories( showLocations_2 );
}

function showLocations_2(){
	_loadLocations( _showLocations );
}

function _showLocations(){
	
	html = "<h1 id='scrollTarget'>" + LANG('locations') + ":</h1>";
	html += LANG('location_select_tip');
	
	// show locations
	for( i=0; i < _locations.length; i++ ){
		html += "\n<div class='groupitem " + (_location == _locations[i]['id'] ? "yellow" : "") + "'><div class='table'>"
			+ "<span class='group_left' onclick='selectLocation(" + _locations[i]['id'] + ")'>"
			+ _locations[i]['name'] + "</span>"
			+ "<span class='inline_text hidetext errortext' id='location_name_error_" + _locations[i]['id'] + "'>" + LANG('location_name_error') + "</span>"
			+ "<span class='inline_text hidetext'>" + LANG('location_name') + ": "
			+ "<input type='text' id='editlocation_" + _locations[i]['id'] + "' /></span>"
			+ " <a href='javascript: editLocation(" + _locations[i]['id'] + ")' class='button green'>" + LANG('edit') + "</a>"
			+ " <a href='javascript: showLocationStock(" + _locations[i]['id'] + ")' class='button orange'>" + LANG('details') + "</a>"
			+ " <a href='javascript: deleteLocation(" + _locations[i]['id'] + ")' class='button red'>" + LANG('delete') + "</a>"
			+ "</div><div class='hidetext'><span class='table_cell' id='location_stock_" + _locations[i]['id'] + "' class='tinytext'></span></div>"
			+ "</div>";
		
		// load stock info
		_loadLocationStockInfo( _locations[i]['id'] );
	}
	
	// show form to add location
	html += "<h1>" + LANG('add_location') + ":</h1>\n"
		+ "<div class='groupitem'><span class='group_left'>"
		+ LANG('location_name') + ": <input type='text' id='addLocation' onkeypress='if(event.keyCode == 13) addLocation();' /></span>"
		+ "<span class='inline_text errortext hidetext' id='location_name_missing'>" + LANG('location_name_missing') + "</span>"
		+ "<span class='inline_text errortext hidetext' id='location_name_error'>" + LANG('location_name_error') + "</span>"
		+ "<a href='javascript: addLocation()' class='button'>" + LANG('add_location') + "</a>" 
		+ "</div>";
	
	// show locations
	showHtml(html);
	
	$.scrollTo( document.getElementById('scrollTarget') );
	
}

function getLocation(id){
	if( id != null ){
		for( i=0; i < _locations.length; i++){
			if( _locations[i]['id'] == id ){
				return _locations[i];
			}
		}
	}
	
	return null;
}

function editLocation(id){
	name = document.getElementById( 'editlocation_' + id ).value.trim()
	vLocation = getLocation(id);
	
	document.getElementById( 'location_name_error_' + id ).style.display = "none";
	
	if( name.length > 0 ){
		get( 	{'function': 'editLocation', 'id': id, 'name': base64_encode(name)},
				function(data, status){
					if( status == "success" && data == "ok" ){
						_loadLocations( _showLocations );
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

function selectLocation(id){
	if( _location == id ){
		_location = null;
	} else {
		_location = id;
	}
	
	_showLocations();
}

function addLocation(){
	document.getElementById( 'location_name_missing' ).style.display = "none";
	document.getElementById( 'location_name_error' ).style.display = "none";
	
	name = document.getElementById( 'addLocation' ).value.trim();
	if( name.length > 0 ){
		get( 	{'function': 'addLocation', 'name': base64_encode(name)},
				function(data, status){
			if( status == "success" && data == "ok" ){
				_loadLocations( _showLocations );
			} else {
				document.getElementById( 'location_name_error' ).style.display = "table-cell";
			}
		});
	} else {
		document.getElementById( 'location_name_missing' ).style.display = "table-cell";
	}
}

function deleteLocation(id){
	get( {'function': 'deleteLocation', 'id': id}, function(){ _loadLocations( _showLocations ); });
}

function _loadLocationStockInfo(location){
	get( {'function': 'getLocationStockInfo', 'location': location},
			function(data, status){
				while( !document.getElementById( 'location_stock_' + location ) );
				document.getElementById( 'location_stock_' + location ).innerHTML = "";
			
				if( status == "success" ){
					stock = JSON.parse(data);
					for( var i=0; i < stock.length; i++ ) {
						hierarchy = getCategoryHierrachy( stock[i]['category'] )
						document.getElementById( 'location_stock_' + location ).innerHTML =
							document.getElementById( 'location_stock_' + location ).innerHTML + (i != 0 ? "<br />" : "")
							+ hierarchy + " (" + stock[i]['total'] + LANG('pieces_short') + ")";
					}				
				}
			} );
}

function showLocationStock(vLocation){
	if( document.getElementById( 'location_stock_' + vLocation ).parentElement.style.display != "block" ){
		document.getElementById( 'location_stock_' + vLocation ).parentElement.style.display = "block";
	} else {
		document.getElementById( 'location_stock_' + vLocation ).parentElement.style.display = "none"; 
	}
}
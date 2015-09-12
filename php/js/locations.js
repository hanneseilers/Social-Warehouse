function showLocations($warehouseId){
	setWarehouseId($warehouseId);
	_loadLocations( _showLocations );
}

function _showLocations(){
	
	html = "<h1 id='scrollTarget'>" + LANG('locations') + ":</h1>";
	html += LANG('location_select_tip');
	
	// show locations
	for( i=0; i < _locations.length; i++ ){
		html += "\n<div class='groupitem " + (_location == _locations[i]['id'] ? "yellow" : "") + "'>"
			+ "<span class='group_left' onclick='selectLocation(" + _locations[i]['id'] + ")'>"
			+ _locations[i]['name'] + "</span>"
			+ "<span class='inline_text table_cell'>" + LANG('location_name') + ": "
			+ "<input type='text' id='editlocation_" + _locations[i]['id'] + "' value='" + _locations[i]['name'] + "' /></span>"
			+ " <a href='javascript: editLocation(" + _locations[i]['id'] + ")' class='button green'>" + LANG('edit') + "</a>"
			+ " <a href='javascript: deleteLocation(" + _locations[i]['id'] + ")' class='button red'>" + LANG('delete') + "</a>"
			+ "</div>";
	}
	
	// show form to add location
	html += "<h1>" + LANG('add_location') + ":</h1>\n"
		+ "<div class='groupitem'><span class='group_left'>"
		+ LANG('location_name') + ": <input type='text' id='addLocation' /></span>"
		+ "<span class='inline_text errortext hidetext' id='location_name_missing'>" + LANG('location_name_missing') + "</span>"
		+ "<span class='inline_text errortext hidetext' id='location_name_error'>" + LANG('location_name_error') + "</span>"
		+ "<a href='javascript: addLocation()' class='button'>" + LANG('add_location') + "</a>" 
		+ "</div>";
	
	// show locations
	showHtml(html);
	
	$.scrollTo( document.getElementById('scrollTarget') );
	
}

function getLocation(id){
	for( i=0; i < _locations.length; i++){
		if( _locations[i]['id'] == id ){
			return _locations[i];
		}
	}
	
	return null;
}

function editLocation(id){
	name = document.getElementById( 'editlocation_' + id ).value.trim()
	if( name.length > 0 ){
		get( 	{'function': 'editLocation', 'id': id, 'name': base64_encode(name)},
				function(data, status){
					if( status == "success" && data == "ok" ){
						_loadLocations( _showLocations );
					}
		});
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
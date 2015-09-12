function showPalettes(warehouseId){
	setWarehouseId( warehouseId );
	_loadPalettes( _showPalettes );
}

function _showPalettes(){
	html = "<h1 id='scrollTarget'>" + LANG('palettes') + ":</h1>";
	html += LANG('palette_select_tip');
	
	// show palettes
	for( i=0; i < _palettes.length; i++ ){
		html += "\n<div class='groupitem " + (_palette == _palettes[i]['id'] ? "yellow" : "") + "'>"
			+ "<span class='group_left' onclick='selectPalette(" + _palettes[i]['id'] + ")'>"
			+ _palettes[i]['name'] + "</span>"
			+ "<span class='inline_text hidetext'>" + LANG('palette_name') + ": "
			+ "<input type='text' id='editpalette_" + _palettes[i]['id'] + "' /></span>"
			+ " <a href='javascript: editPalette(" + _palettes[i]['id'] + ")' class='button green'>" + LANG('edit') + "</a>"
			+ " <a href='javascript: deletePalette(" + _palettes[i]['id'] + ")' class='button red'>" + LANG('delete') + "</a>"
			+ "</div>";
	}
	
	// show form to add palette
	html += "<h1>" + LANG('add_palette') + ":</h1>\n"
		+ "<div class='groupitem'><span class='group_left'>"
		+ LANG('palette_name') + ": <input type='text' id='addPalette' /></span>"
		+ "<span class='inline_text errortext hidetext' id='palette_name_missing'>" + LANG('palette_name_missing') + "</span>"
		+ "<span class='inline_text errortext hidetext' id='palette_name_error'>" + LANG('palette_name_error') + "</span>"
		+ "<a href='javascript: addPalette()' class='button'>" + LANG('add_palette') + "</a>" 
		+ "</div>";
	
	// show palettes
	showHtml(html);
	
	$.scrollTo( document.getElementById('scrollTarget') );
}

function getPalette(id){
	for( i=0; i < _palettes.length; i++){
		if( _palettes[i]['id'] == id ){
			return _palettes[i];
		}
	}
	
	return null;
}

function editPalette(id){
	name = document.getElementById( 'editpalette_' + id ).value.trim()
	vPalette = getPalette(id);
	
	if( name.length > 0 ){
		get( 	{'function': 'editPalette', 'id': id, 'name': base64_encode(name)},
				function(data, status){
					if( status == "success" && data == "ok" ){
						_loadPalettes( _showPalettes );
						document.getElementById( 'editpalette_' + id ).parentElement.style.display = "none";
					}
		});
	} else if( vPalette ) {
		document.getElementById( 'editpalette_' + id ).value = vPalette['name'];
		document.getElementById( 'editpalette_' + id ).parentElement.style.display = "table-cell";
	}
}

function selectPalette(id){
	if( _palette == id ){
		_palette = null;
	} else {
		_palette = id;
	}
	
	_showPalettes();
}

function addPalette(){
	document.getElementById( 'palette_name_missing' ).style.display = "none";
	document.getElementById( 'palette_name_error' ).style.display = "none";
	
	name = document.getElementById( 'addPalette' ).value.trim();
	if( name.length > 0 ){
		get( 	{'function': 'addPalette', 'name': base64_encode(name)},
				function(data, status){
			if( status == "success" && data == "ok" ){
				_loadPalettes( _showPalettes );
			} else {
				document.getElementById( 'palette_name_error' ).style.display = "table-cell";
			}
		});
	} else {
		document.getElementById( 'palette_name_missing' ).style.display = "table-cell";
	}
}

function deletePalette(id){
	get( {'function': 'deletePalette', 'id': id}, function(){ _loadPalettes( _showPalettes ); });
}
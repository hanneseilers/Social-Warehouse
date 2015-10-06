function showPalettes(warehouseId){
	setWarehouseId( warehouseId );
	_loadRestricted( showPalettes_2 );	
}

function showPalettes_2(){
	_loadCategories( showPalettes_3 );	
}

function showPalettes_3(){
	_loadLocations( showPalettes_4 );
}

function showPalettes_4(){
	_loadPalettes( _showPalettes );
}

function _showPalettes(){
	
	// get set location info
	var gLocation = getLocation( _location );
	
	// show form to add palette
	var html = "<h1>" + LANG('add_palette') + ":</h1>\n"
		+ "<div class='groupitem'><span class='group_left'>"
		+ LANG('palette_name') + ": <input type='text' id='addPalette' onkeypress='if(event.keyCode == 13) addPalette()'; /></span>"
		+ "<span class='inline_text errortext hidetext' id='palette_name_missing'>" + LANG('palette_name_missing') + "</span>"
		+ "<span class='inline_text errortext hidetext' id='palette_name_error'>" + LANG('palette_name_error') + "</span>"
		+ "<a href='javascript: addPalette()' class='button'>" + LANG('add_palette') + "</a>" 
		+ "</div>";
	
	
	html += "<h1 id='scrollTarget'>" + LANG('palettes') + ":</h1>";
	html += LANG('palette_select_tip');
	html += "<div class='hightlimited' id='paletteScrollWindow'>";
	
	
	// show palettes
	for( var i=0; i < _palettes.length; i++ ){
		// get location
		var vLocation = getLocation( _palettes[i]['location'] );
		
		// create html
		html += "\n<div id='paletteitem_" + _palettes[i]['id'] + "' class='groupitem " + (_palette == _palettes[i]['id'] ? "yellow" : "") + "'><div class='table'>"
			+ "<span class='group_left text_bold' onclick='selectPalette(" + _palettes[i]['id'] + ")'>"
			+ _palettes[i]['name'] + (vLocation ? " : " + vLocation['name'] : "")
			+ (_palettes[i]['cleared'] == 1 ? " " + LANG('palette_cleared') : "")
			+ "</span>"
			+ "<span class='inline_text table_cell hidetextif( !_restricted ){'>" + LANG('palette_name') + ": "
			+ "<input type='text' id='editpalette_" + _palettes[i]['id'] + "' /></span>"
			+ " <a href='javascript: editPalette(" + _palettes[i]['id'] + ")' class='button green'>" + LANG('edit') + "</a>"
			+ " <a href='javascript: showPaletteStock(" + _palettes[i]['id'] + ")' class='button orange'>" + LANG('details') + "</a>"
			+ " <a href='barcode.php?"
				+ "paletteID=" + _palettes[i]['id']
				+ "&paletteName=" + base64_encode( _palettes[i]['name'] )
				+ "&warehouseID=" + _warehouseId
			+ "' target='_blanc' class='button'>" + LANG('print') + "</a>"
			+ (_palettes[i]['cleared'] == 0 ? " <a href='javascript: clearPalette(" + _palettes[i]['id'] + ")' class='button red'>" + LANG('clear') + "</a>" : "")
			+ "</div>";
		
		// stock info
		html += "<div class='hidetext'>"
			+ "<span class='table_cell' id='palette_stock_" + _palettes[i]['id'] + "' class='tinytext'></span></div>"
			+ "<div class='hidetext' id='palette_move_" + _palettes[i]['id'] + "'>"
			+ "<a href='javascript: movePalette(" + _palettes[i]['id'] + ")' class='button'>"
			+ (gLocation ? LANG('move_palette') + " " + gLocation['name'] : LANG('palette_location_remove') ) + "</a>";
		
		if( !_restricted ){
			html += " <a href='javascript: deletePalette(" + _palettes[i]['id'] + ")' class='button red'>" + LANG('delete') + "</a>";
		}
		
		html += "</div></div>";
		
		// load stock info
		_loadPaletteStockInfo( _palettes[i]['id'] );
	}
	
	html += "</div>";
	
	// show palettes
	showHtml(html);
	
	// scroll to selected palette or to scrollTarget
	scrollPalette = document.getElementById( 'paletteitem_' + _palette );
	scrollContainer = document.getElementById( 'paletteScrollWindow' );
	if( scrollPalette && scrollContainer ){
		$(window).scrollTo( scrollContainer );
		$(scrollContainer).scrollTo( scrollPalette );
	}
	else
		$.scrollTo( document.getElementById('scrollTarget') );
}

function getPalette(id){
	for( var i=0; i < _palettes.length; i++){
		if( _palettes[i]['id'] == id ){
			return _palettes[i];
		}
	}
	
	return null;
}

function editPalette(id){
	var name = document.getElementById( 'editpalette_' + id ).value.trim()
	var vPalette = getPalette(id);
	
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
		palette = getPalette(id);
		_location = palette['location'];
	}
	
	_showPalettes();
}

function addPalette(){
	document.getElementById( 'palette_name_missing' ).style.display = "none";
	document.getElementById( 'palette_name_error' ).style.display = "none";
	
	var name = document.getElementById( 'addPalette' ).value.trim();
	if( name.length > 0 ){
		get( 	{'function': 'addPalette', 'name': base64_encode(name)},
				function(data, status){
			data = data.split(';');
			if( status == "success" && data.length > 1 && data[0] == "ok" ){
				_palette = Number(data[1]);
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

function clearPalette(id){
	get( {'function': 'clearPalette', 'id': id}, function(){ _loadPalettes( _showPalettes ); });
}

function movePalette(palette){
	get( {'function': 'movePalette', 'palette': palette, 'location': (_location ? _location : "NULL")},
			function(data, status){
				showPalettes( _warehouseId );
			});
}

function _loadPaletteStockInfo(palette){
	get( {'function': 'getPaletteStockInfo', 'palette': palette},
			function(data, status){
				while( !document.getElementById( 'palette_stock_' + palette ) );
				document.getElementById( 'palette_stock_' + palette ).innerHTML = "";
			
				if( status == "success" ){
					var stock = JSON.parse(data);
					
					// add stock info
					for( var i=0; i < stock.length; i++ ) {
						var hierarchy = getCategoryHierrachy( stock[i]['category'] )
						if( stock[i]['total'] && stock[i]['total'] > 0 ){
							document.getElementById( 'palette_stock_' + palette ).innerHTML = document.getElementById( 'palette_stock_' + palette ).innerHTML
								+ (i != 0 ? "<br />" : "")
								+ hierarchy
								+ " (" + stock[i]['total'] + " " + getUnit(stock[i]) + ")";
						}
					}
				}
			} );
}

function showPaletteStock(palette){
	if( document.getElementById( 'palette_stock_' + palette ).parentElement.style.display != "block" ){
		document.getElementById( 'palette_stock_' + palette ).parentElement.style.display = "block";
		document.getElementById( 'palette_move_' + palette ).style.display = "block";
	} else {
		document.getElementById( 'palette_stock_' + palette ).parentElement.style.display = "none";
		document.getElementById( 'palette_move_' + palette ).style.display = "none";
	}
}
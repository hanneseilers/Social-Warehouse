_warehouseId = 0;
_rootId = null;
_categories = [];
_locations = [];
_location = null;
_palettes = [];
_palette = null

function setWarehouseId(id){
	_warehouseId = id;
	document.getElementById( 'loading' ).style.display = 'block';
	document.getElementById( 'datacontent' ).style.display = 'none';
}

function showHtml(html){
	document.getElementById( 'loading' ).style.display = 'none';
	document.getElementById( 'datacontent' ).style.display = 'block';
	document.getElementById( 'datacontent' ).innerHTML = html;
}

function _loadCategories(callback, arg){	
	get( {	'function': 'getCategories',
			'location': (_location ? _location : "NULL"),
			'palette': (_palette ? _palette : "NULL"),
			},
			function(data, status){
		if( status == "success" ){
			_categories = JSON.parse(data);
		}
		
		if( callback ){
			if( arg ){
				callback(arg);
			} else {
				callback()
			}
		}
	} );
}

function _loadLocations(callback, arg){
	get( {'function': 'getLocations'}, function(data, status){
		if( status == "success" ){
			_locations = JSON.parse(data);
		}
		
		if( callback ){
			if( arg ){
				callback(arg);
			} else {
				callback()
			}
		}
	} );
}

function _loadPalettes(callback, arg){
	get( {'function': 'getPalettes'}, function(data, status){
		if( status == "success" ){
			_palettes = JSON.parse(data);
		}
		
		if( callback ){
			if( arg ){
				callback(arg);
			} else {
				callback()
			}
		}
	} );
}
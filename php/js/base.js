_warehouseId = 0;
_rootId = null;
_categories = [];
_locations = [];
_location = null;
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

function _loadCategories(callback, arg=null){
	get( {'function': 'getCategories'}, function(data, status){
		if( status == "success" ){
			_categories = JSON.parse(data);
		}
		
		if( arg ){
			callback(arg);
		} else {
			callback()
		}
	} );
}

function _loadLocations(callback, arg=null){
	get( {'function': 'getLocations'}, function(data, status){
		if( status == "success" ){
			_locations = JSON.parse(data);
		}
		
		if( arg ){
			callback(arg);
		} else {
			callback()
		}
	} );
}
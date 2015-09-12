_warehouseId = 0;
_rootId = null;
_categories = [];
_locations = [];
_location = null;
_palette = null

_addCallback = null;
_isLooseStock = false;

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

function _loadCategories(){
	get( {'function': 'getCategoryInfos'}, function(data, status){
		if( status == "success" ){
			_categories = JSON.parse(data);
		}
		_showCategories();
	} );
}
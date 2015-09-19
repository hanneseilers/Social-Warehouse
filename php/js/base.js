var _warehouseId = 0;
var _rootId = null;
var _warehouse = null;
var _categories = [];
var _locations = [];
var _location = null;
var _palettes = [];
var _palette = null
var _restricted = false;

get = function (data, callback){
	$.get( "api/api.php", data, callback );
}

function login(id){
	
	var vPasswordInput = document.getElementById( 'warehousepw' + id );	
	
	// hide all password inputs
	var vElements = document.getElementsByClassName('loginpw');
	document.getElementById( 'warehouseloginfailed' + id ).style.display = "none";
	for( var i=0; i < vElements.length; i++ ){
		vElements[i].style.display = "none";
		

		if( vElements[i].lastChild != vPasswordInput )
			vElements[i].lastChild.value = "";
	}
	
	if( !vPasswordInput.value.length ){		
		// show selected entry
		vPasswordInput.parentElement.style.display = "table-cell";
		vPasswordInput.focus();
	} else {
		
		// show wait
		document.getElementById( 'warehouselogin' + id ).style.display = "none";
		document.getElementById( 'warehousedemand' + id ).style.display = "none";
		document.getElementById( 'warehouseload' + id ).style.display = "table-cell";
		vPasswordInput.parentElement.style.display = 'none';
		
		// check if password ok
		var vPassword = MD5( vPasswordInput.value );
		get( {'function': 'checkLogin', 'warehouse': id, 'pw': vPassword}, login_result );
		
	}
}

function login_result(data, status, xhr){
	data = data.split(";");
	if( status == "success" && data.length > 0 && data[0] == "ok" ){		
		location.reload();		
	} else {
		var id = data[1];
		document.getElementById( 'warehouseloginfailed' + id ).style.display = "block";
		document.getElementById( 'warehousepw' + id ).parentElement.style.display = "table-cell";
		document.getElementById( 'warehouselogin' + id ).style.display = "table-cell";
		document.getElementById( 'warehousedemand' + id ).style.display = "table-cell";
		document.getElementById( 'warehouseload' + id ).style.display = "none";
	}
}

function logout(){
	get( {'function': 'logout'},	function(){location.reload();} );
}

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

function _loadWarehouse(callback, arg){
	get( {	'function': 'getWarehouse', 'id': (_warehouseId ? _warehouseId : "NULL") },
		function(data, status){
			if( status == "success" ){
				_warehouse = JSON.parse(data);
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

function _loadCategories(callback, arg){
	get( {	'function': 'getCategories',
			'location': (_location ? _location : "NULL"),
			'palette': (_palette ? _palette : "NULL")},
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

function _loadRestricted(callback, arg){
	get( {'function': 'checkRestricted'}, function(data, status){
		if( status == "success" && data == "ok" ){
			_restricted = true;
		} else {
			_restricted = false;
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
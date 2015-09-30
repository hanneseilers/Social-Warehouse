var _warehouseId = 0;
var _rootId = null;
var _warehouse = null;
var _categories = [];
var _locations = [];
var _location = null;
var _palettes = [];
var _palette = null
var _restricted = false;
var _cacheTimeoutTimer = null;

get = function (data, callback){
	$.get( "api/api.php", data, callback );
	resetCacheTimeout();
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
		
		var url = window.location.href;
		window.location.href = url.replace("&timeout=1", "").replace("timeout=1", "");
		
	} else {
		var id = data[1];
		document.getElementById( 'warehouseloginfailed' + id ).style.display = "block";
		document.getElementById( 'warehousepw' + id ).parentElement.style.display = "table-cell";
		document.getElementById( 'warehouselogin' + id ).style.display = "table-cell";
		document.getElementById( 'warehousedemand' + id ).style.display = "table-cell";
		document.getElementById( 'warehouseload' + id ).style.display = "none";
	}
}

function logout(timeout){	
	get( {'function': 'logout'},	function(){
		// check if session timed out
		if( timeout ){	
			
			// set url flag
			var url = window.location.href;
			url = url.replace("&timeout=1", "").replace("timeout=1", "");
			
			if( url.indexOf('?') == url.length-1 ) url = url.replace("?", "");
			
			if (url.indexOf('?') > -1) url += '&timeout=1';
			else url += '?timeout=1';
				
			window.location.href = url;
			
		} else {
			location.reload(true);
		}
	} );
}

function startCacheTimer(){
	var timeout = parseInt(document.getElementById( 'gc_maxtime' ).innerHTML) - 60;
	_cacheTimeoutTimer = window.setTimeout( function(){ logout(true); }, timeout*1000 );
}

function resetCacheTimeout(){
	window.clearTimeout( _cacheTimeoutTimer );
	startCacheTimer();
}

function hideTimedoutMessage(){
	document.getElementById( 'timeout_message' ).style.display = 'none';
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
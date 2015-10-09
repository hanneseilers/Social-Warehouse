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
var _tap = 0;

var _barcodeCache = "";
var _barcodeEnabled = false;

get = function (data, callback){
	$.get( "api/api.php", data, callback );
	resetCacheTimeout();
}

String.prototype.endsWith = function(suffix) {
    return this.indexOf(suffix, this.length - suffix.length) !== -1;
};

String.prototype.startsWith = function (str){
    return this.indexOf(str) === 0;
  };

function registerBarcodeScanner(){
	document.addEventListener("keypress", checkForBarcode, true);
	console.debug( 'Registered barcode keylogger' );
}

function checkForBarcode(event){
	if( event ){
		// set barcode cache
		var key = String.fromCharCode( event.keyCode );
		if( _barcodeCache.length == 0 && key == '+' ){
			_barcodeEnabled = true;
		}
		
		if( _barcodeEnabled ){
			// add key to barcode cache
			_barcodeCache += key
		
			// analyse cache
			if( _barcodeCache.startsWith( '++SW' ) && _barcodeCache.endsWith( '++' ) ){
				var command = _barcodeCache.substr( 2, _barcodeCache.length-4 );				
				
				if( command.startsWith('SWP') ){
					
					// select palette
					var paletteId = command.substr( 3, command.length-1 );
					console.debug( "Palette " + paletteId + " scanned" );
					
					// check if to reset
					if( paletteId == 0 )
						paletteId = null;
					
					selectPalette( paletteId, (_tap == 3) );
					updateStockLocation();
					
				} else if( command.startsWith('SWL') ){
					
					// select location
					var locationId = command.substr( 3, command.length-1 );
					console.debug( "Location " + locationId + " scanned" );
					
					// check if to reset
					if( locationId == 0 )
						locationId = null;
					
					selectLocation( locationId, (_tap == 2) );
					updateStockLocation();
					
				} else if( command = "MVP" ){
					
					// move palette				
					if( _palette != null && _location != null ){
						movePalette( _palette, updateStockLocation );
						console.debug( "Moved palette " + _palette + " to " + _location + " scanned" );
					}
					
				}
				
				_barcodeCache = "";
				_barcodeEnabled = false;
			}
			
		}
	}
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

function showHtml(html){
	document.getElementById( 'loading' ).style.display = 'none';
	document.getElementById( 'datacontent' ).style.display = 'block';
	document.getElementById( 'datacontent' ).innerHTML = html;
}



function loadData(warehouseId){
	document.getElementById( 'loading' ).style.display = 'block';
	document.getElementById( 'datacontent' ).style.display = 'none';
	
	registerBarcodeScanner();
	
	console.debug( 'Loading warehouse ' + warehouseId );
	_warehouseId = warehouseId;
	_loadWarehouse( loadData_1 );
	
}

function loadData_1(){
	console.debug( 'Loading categories' );
	_loadRestricted( loadData_2 );	
}

function loadData_2(){
	console.debug( 'Loading categories' );
	_loadCategories( loadData_3 );
}

function loadData_3(){
	console.debug( 'Loading locations' );
	_loadLocations( loadData_4 );
}

function loadData_4(){
	console.debug( 'Loading palettes' );
	_loadPalettes();
	
	document.getElementById( 'loading' ).style.display = 'none';
	document.getElementById( 'datacontent' ).style.display = 'block';
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
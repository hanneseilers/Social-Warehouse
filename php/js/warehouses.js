function addWarehouse(){
	name = document.getElementById( 'warehousename' ).value.trim();
	password = document.getElementById( 'password' ).value.trim();
	password2 = document.getElementById( 'password-repeat' ).value.trim();
	description = document.getElementById( 'warehousedescription' ).value.trim();
	country = document.getElementById( 'country' ).value.trim();
	city = document.getElementById( 'city' ).value.trim();

	// hide errors
	document.getElementById( 'warehousewrong' ).style.display = "none";
	document.getElementById( 'passwordmissing' ).style.display = "none";
	document.getElementById( 'passwordwrong' ).style.display = "none";
	document.getElementById( 'citymissing' ).style.display = "none";
	
	// check password
	if( password.length == 0){
		document.getElementById( 'passwordmissing' ).style.display = "block";
	} else if( password != password2 ){
		document.getElementById( 'passwordwrong' ).style.display = "block";
	}
	// check city
	else if( city.length == 0 ) {
		document.getElementById( 'citymissing' ).style.display = "block";
	}
	
		
	// try to create new warehouse
	else {
		password = MD5(password);
		get( 	{	'function': 'addWarehouse',
					'name': base64_encode(name),
					'desc': base64_encode(description),
					'pw': password,
					'country': base64_encode(country),
					'city': base64_encode(city)
				},
				function(data, status){
					data = data.split(";");
					if( status == "success" && data.length > 0 && data[0] == "ok" )
						location.reload();
					else
						document.getElementById( 'warehousewrong' ).style.display = "block";
				});
	}
	
}

function deleteWarehouse(){
	get( {'function': 'deleteWarehouse'}, function(){ location.reload(); } );
}

function editWarehouse(){
	
	tableEdit = document.getElementById( 'warehouseeditdata' );
	
	// hide errors
	document.getElementById( 'passwordwrong' ).style.display = "none";
	document.getElementById( 'citymissing' ).style.display = "none";
	document.getElementById( 'warehouse_name_missing' ).style.display = "none";
	document.getElementById( 'warehouse_name_error' ).style.display = "none";
	
	if( tableEdit.style.display != "block" ){
		tableEdit.style.display = "block";
	} else {	
		name = document.getElementById( 'warehousenamenew' ).value.trim();
		password = document.getElementById( 'password' ).value.trim();
		password2 = document.getElementById( 'password-repeat' ).value.trim();
		description = document.getElementById( 'warehousedescription' ).value.trim();
		country = document.getElementById( 'country' ).value.trim();
		city = document.getElementById( 'city' ).value.trim();
		
		if( password != password2 ){
			document.getElementById( 'passwordwrong' ).style.display = "block";
		} else if( city.length == 0 ){
			document.getElementById( 'citymissing' ).style.display = "block";
		} else {
			
			if( password.length > 0 )
				password = MD5(password);
			
			if( name.length > 0 ){
				get( 	{
							'function': 'editWarehouse',
							'name': base64_encode(name),
							'desc': base64_encode(description),
							'pw': password,
							'country': base64_encode(country),
							'city': base64_encode(city)
						},
						function(data, status){
							if( status == "success" && data == "ok" ){
								location.reload();
							} else {
								document.getElementById( 'warehouse_name_error' ).style.display = "table-cell";
							}
						} );
			} else {
				document.getElementById( 'warehouse_name_missing' ).style.display = "table-cell";
			}
			
		}
	}
}

function showWarehouseDescription(id){
	// show loading
	document.getElementById( 'overlay_text' ).style.display = "none";
	document.getElementById( 'overlay_loading' ).style.display = "block";
	document.getElementById( 'overlay' ).style.display = "block";
	
	// get description
	get( 	{'function': 'getWarehouseDescription', 'warehouse': id},
			function(data, status){
				data = data.split(";");
				if( status == "success" && data.length > 0 && data[0] == "ok" )
					document.getElementById( 'overlay_text' ).innerHTML = data[1];
				else
					document.getElementById( 'overlay_text' ).innerHTML = "no description found";
				
				document.getElementById( 'overlay_text' ).style.display = "block";
				document.getElementById( 'overlay_loading' ).style.display = "none";
			});
	
}

function moveWarehouseDescription(event){
    var dot, eventDoc, doc, body, pageX, pageY;

    event = event || window.event; // IE-ism

    // If pageX/Y aren't available and clientX/Y are,
    // calculate pageX/Y - logic taken from jQuery.
    // (This is to support old IE)
    if (event.pageX == null && event.clientX != null) {
        eventDoc = (event.target && event.target.ownerDocument) || document;
        doc = eventDoc.documentElement;
        body = eventDoc.body;

        event.pageX = event.clientX +
          (doc && doc.scrollLeft || body && body.scrollLeft || 0) -
          (doc && doc.clientLeft || body && body.clientLeft || 0);
        event.pageY = event.clientY +
          (doc && doc.scrollTop  || body && body.scrollTop  || 0) -
          (doc && doc.clientTop  || body && body.clientTop  || 0 );
    }

    document.getElementById( 'overlay' ).style.top = event.pageY;
    document.getElementById( 'overlay' ).style.left = event.pageX;
}

function hideWarehouseDescription(){
	document.getElementById( 'overlay' ).style.display = "none";
}

function filterCountry(){
	country = document.getElementById( 'filtercountry' ).value;
}

function filterCity(){
	city = document.getElementById( 'filtercity' ).value;
}

function getStyleRuleValue(style, selector, sheet) {
    var sheets = typeof sheet !== 'undefined' ? [sheet] : document.styleSheets;
    for (var i = 0, l = sheets.length; i < l; i++) {
        var sheet = sheets[i];
        if( !sheet.cssRules ) { continue; }
        for (var j = 0, k = sheet.cssRules.length; j < k; j++) {
            var rule = sheet.cssRules[j];
            if (rule.selectorText && rule.selectorText.split(',').indexOf(selector) !== -1) {
                return rule.style[style];
            }
        }
    }
    return null;
}

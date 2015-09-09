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
	} else {
		
		// show wait
		document.getElementById( 'warehouselogin' + id ).style.display = "none";
		document.getElementById( 'warehousereq' + id ).style.display = "none";
		document.getElementById( 'warehouseload' + id ).style.display = "table-cell";
		vPasswordInput.parentElement.style.display = 'none';
		
		// check if password ok
		var vPassword = MD5( vPasswordInput.value );
		$.get( "api.php", {'function': 'checkLogin', 'warehouse': id, 'pw': vPassword}, login_result );
		
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
		document.getElementById( 'warehouseload' + id ).style.display = "none";
	}
}

function logout(){
	$.get( "api.php", {'function': 'logout'}, function(){location.reload();} );
}

function addWarehouse(){
	$name = document.getElementById( 'warehousename' ).value.trim();
	$password = document.getElementById( 'password' ).value.trim();
	$password2 = document.getElementById( 'password-repeat' ).value.trim();
	$description = document.getElementById( 'warehousedescription' ).value.trim();
	$country = document.getElementById( 'country' ).value.trim();
	$city = document.getElementById( 'city' ).value.trim();

	// hide errors
	document.getElementById( 'warehousewrong' ).style.display = "none";
	document.getElementById( 'passwordmissing' ).style.display = "none";
	document.getElementById( 'passwordwrong' ).style.display = "none";
	document.getElementById( 'citymissing' ).style.display = "none";
	
	// check password
	if( $password.length == 0){
		document.getElementById( 'passwordmissing' ).style.display = "block";
	} else if( $password != $password2 ){
		document.getElementById( 'passwordwrong' ).style.display = "block";
	}
	// check city
	else if( $city.length == 0 ) {
		document.getElementById( 'citymissing' ).style.display = "block";
	}
	
		
	// try to create new warehouse
	else {
		$password = MD5($password);
		$.get( 	"api.php",
				{	'function': 'addWarehouse',
					'name': base64_encode($name),
					'desc': base64_encode($description),
					'pw': $password,
					'country': base64_encode($country),
					'city': base64_encode($city)
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
	$.get( "api.php", {'function': 'deleteWarehouse'}, function(){ location.reload(); } );
}

function changeWarehouseInfo(){
	
	$tableEdit = document.getElementById( 'warehouseeditdata' );
	if( $tableEdit.style.display != "block" ){
		$tableEdit.style.display = "block";
	} else {	
		$name = document.getElementById( 'warehousenamenew' ).value.trim();
		$password = document.getElementById( 'password' ).value.trim();
		$password2 = document.getElementById( 'password-repeat' ).value.trim();
		$description = document.getElementById( 'warehousedescription' ).value.trim();
		
		if( $password != $password2 ){
			document.getElementById( 'passwordwrong' ).style.display = "block";
		} else {
			if( $password.length > 0 )
				$password = MD5($password);
				
			$.get( 	"api.php",
					{'function': 'changeWarehouseInfo', 'name': base64_encode($name), 'desc': base64_encode($description), 'pw': $password},
					function(data, success){
						location.reload();
					} );
		}
	}
}

function showWarehouseDescription($id){
	// show loading
	document.getElementById( 'warehousedescriptiontext' ).style.display = "none";
	document.getElementById( 'descriptionloading' ).style.display = "block";
	document.getElementById( 'warehousedescriptionoverlay' ).style.display = "block";
	
	// get description
	$.get( 	"api.php",
			{'function': 'getWarehouseDescription', 'warehouse': $id},
			function(data, status){
				data = data.split(";");
				if( status == "success" && data.length > 0 && data[0] == "ok" ){
					document.getElementById( 'warehousedescriptiontext' ).innerHTML = data[1];
					document.getElementById( 'warehousedescriptiontext' ).style.display = "block";
					document.getElementById( 'descriptionloading' ).style.display = "none";
				}
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

    document.getElementById( 'warehousedescriptionoverlay' ).style.top = event.pageY;
    document.getElementById( 'warehousedescriptionoverlay' ).style.left = event.pageX;
}

function hideWarehouseDescription(){
	document.getElementById( 'warehousedescriptionoverlay' ).style.display = "none";
}
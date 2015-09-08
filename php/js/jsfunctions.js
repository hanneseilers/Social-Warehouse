function login(id){
	
	var vPasswordInput = document.getElementById( 'grouppw' + id );	
	
	// hide all password inputs
	var vElements = document.getElementsByClassName('loginpw');
	document.getElementById( 'grouploginfailed' + id ).style.display = "none";
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
		document.getElementById( 'grouplogin' + id ).style.display = "none";
		document.getElementById( 'groupload' + id ).style.display = "table-cell";
		vPasswordInput.parentElement.style.display = 'none';
		
		// check if password ok
		var vPassword = MD5( vPasswordInput.value );
		$.get( "api.php", {'function': 'checkLogin', 'group': id, 'pw': vPassword}, login_result );
		
	}
}

function login_result(data, status, xhr){
	data = data.split(";");
	if( status == "success" && data.length > 0 && data[0] == "ok" ){		
		location.reload();		
	} else {
		var id = data[1];
		document.getElementById( 'grouploginfailed' + id ).style.display = "block";
		document.getElementById( 'grouppw' + id ).parentElement.style.display = "table-cell";
		document.getElementById( 'grouplogin' + id ).style.display = "table-cell";
		document.getElementById( 'groupload' + id ).style.display = "none";
	}
}

function logout(){
	$.get( "api.php", {'function': 'logout'}, function(){location.reload();} );
}

function changeGroupName(){
	$name = document.getElementById( 'groupnamenew' ).value;
	$.get( "api.php", {'function': 'changeGroupName', 'name': base64_encode($name)}, function(data, success){
		location.reload();
	} );
}
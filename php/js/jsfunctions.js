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
		$.get( "db/checklogin.php", {'group': id, 'pw': vPassword}, login_result );
		
	}
}

function login_result(data, status, xhr){
	data = data.split(";");
	if( status == "success" && data.length > 1 && data[1] == "ok" ){
		
		alert("login");
		
	} else {
		var id = data[0];
		document.getElementById( 'grouploginfailed' + id ).style.display = "block";
		document.getElementById( 'grouppw' + id ).parentElement.style.display = "table-cell";
		document.getElementById( 'grouplogin' + id ).style.display = "table-cell";
		document.getElementById( 'groupload' + id ).style.display = "none";
	}
}
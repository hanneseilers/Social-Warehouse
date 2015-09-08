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

function addGroup(){
	$name = document.getElementById( 'groupname' ).value.trim();
	$password = document.getElementById( 'password' ).value.trim();
	$password2 = document.getElementById( 'password-repeat' ).value.trim();
	$description = document.getElementById( 'groupdescription' ).value.trim();

	// hide errors
	document.getElementById( 'groupwrong' ).style.display = "none";
	document.getElementById( 'passwordmissing' ).style.display = "none";
	document.getElementById( 'passwordwrong' ).style.display = "none";
	
	// check password
	if( $password.length == 0){
		document.getElementById( 'passwordmissing' ).style.display = "block";
	} else if( $password != $password2 ){
		document.getElementById( 'passwordwrong' ).style.display = "block";
		
	// try to create new group
	} else {
		$password = MD5($password);
		$.get( 	"api.php",
				{'function': 'addGroup', 'name': base64_encode($name), 'desc': base64_encode($description), 'pw': $password},
				function(data, status){
					data = data.split(";");
					if( status == "success" && data.length > 0 && data[0] == "ok" )
						location.reload();
					else
						document.getElementById( 'groupwrong' ).style.display = "block";
				});
	}
	
}

function changeGroupInfo(){
	
	$tableEdit = document.getElementById( 'groupeditdata' );
	if( $tableEdit.style.display != "block" ){
		$tableEdit.style.display = "block";
	} else {	
		$name = document.getElementById( 'groupnamenew' ).value.trim();
		$password = document.getElementById( 'password' ).value.trim();
		$password2 = document.getElementById( 'password-repeat' ).value.trim();
		$description = document.getElementById( 'groupdescription' ).value.trim();
		
		if( $password != $password2 ){
			document.getElementById( 'passwordwrong' ).style.display = "block";
		} else {
			if( $password.length > 0 )
				$password = MD5($password);
				
			$.get( 	"api.php",
					{'function': 'changeGroupInfo', 'name': base64_encode($name), 'desc': base64_encode($description), 'pw': $password},
					function(data, success){
						location.reload();
					} );
		}
	}
}

function showGroupDescription($id){
	// show loading
	document.getElementById( 'groupdescriptiontext' ).style.display = "none";
	document.getElementById( 'descriptionloading' ).style.display = "block";
	document.getElementById( 'groupdescription' ).style.display = "block";
	
	// get description
	$.get( 	"api.php",
			{'function': 'getGroupDescription', 'group': $id},
			function(data, status){
				data = data.split(";");
				if( status == "success" && data.length > 0 && data[0] == "ok" ){
					document.getElementById( 'groupdescriptiontext' ).innerHTML = data[1];
					document.getElementById( 'groupdescriptiontext' ).style.display = "block";
					document.getElementById( 'descriptionloading' ).style.display = "none";
				}
			});
	
}

function moveGroupDescription(event){
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

    document.getElementById( 'groupdescription' ).style.top = event.pageY;
    document.getElementById( 'groupdescription' ).style.left = event.pageX;
}

function hideGroupDescription(){
	document.getElementById( 'groupdescription' ).style.display = "none";
}
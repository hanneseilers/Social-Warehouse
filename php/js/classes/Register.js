function Register(){
	
	this.register = function(){
		
		// get data
		var name = document.getElementById('register_name').value;
		var country = document.getElementById('register_country').value;
		var city = document.getElementById('register_city').value;
		var mail = document.getElementById('register_mail').value;
		var password = document.getElementById('register_password').value;
		
		// TODO: check values and register
		
	}
	
}

/**
 * Shows registration form.
 * @param content	Content where to add registration form.
 */
Register.showForm = function(content){
	
	if( content != null ){
		
		// create elements
		var vHeader = document.createElement("h1");
		var vTable = document.createElement("table");
		var vTxtName = document.createElement("input");
		var vTxtCountry = document.createElement("select");
		var vTxtCity = document.createElement("input");
		var vTxtMail = document.createElement("input");
		var vTxtPassword = document.createElement("input");
		var vBtnRegister =document.createElement("a");
		
		// set ids
		vTxtName.id = "register_name";
		vTxtCountry.id ="register_country";
		vTxtCity.id = "register_city";
		vTxtMail.id = "register_mail";
		vTxtPassword.id = "register_password";
		
		// set attributes
		vTable.className="righttext";
		vTxtName.style.width = "100%";
		vTxtCity.style.width = "100%";
		vTxtMail.style.width = "100%";
		vTxtPassword.style.width = "100%";
		
		vTxtMail.type = "email";
		vTxtPassword.type = "password";
		vBtnRegister.className = "button block"
		
		// add content to elements
		vHeader.innerHTML = LANG('register') + ":";
		vBtnRegister.innerHTML = LANG('register_submit');
		vTable.insertRow(-1).insertCell(-1).innerHTML = LANG('register_name') + ":";
		vTable.insertRow(-1).insertCell(-1).innerHTML = LANG('register_country') + ":";
		vTable.insertRow(-1).insertCell(-1).innerHTML = LANG('register_city') + ":";
		vTable.insertRow(-1).insertCell(-1).innerHTML = LANG('register_mail') + ":";
		vTable.insertRow(-1).insertCell(-1).innerHTML = LANG('register_password') + ":";
		vTable.insertRow(-1).insertCell(-1).className="block hspacer";
		vTable.insertRow(-1);
		
		// load countries
		get( 'getCountries', {}, function(data){
			if( data.response ){
				for( i in data.response ){
					var vOption = document.createElement("option");
					vOption.text = data.response[i][0];
					vTxtCountry.add( vOption );
				}
			}			
		} );
		
		// add elements to table
		if( vTable.rows.length > 6 ){
			vTable.rows[0].insertCell(-1).appendChild( vTxtName );
			vTable.rows[1].insertCell(-1).appendChild( vTxtCountry );
			vTable.rows[2].insertCell(-1).appendChild( vTxtCity );
			vTable.rows[3].insertCell(-1).appendChild( vTxtMail );
			vTable.rows[4].insertCell(-1).appendChild( vTxtPassword );
			vTable.rows[6].insertCell(-1).appendChild( vBtnRegister );
			vTable.rows[6].cells[0].colSpan = "2";
			vTable.rows[6].cells[0].className ="centertext";
		}
		
		// add elements to content
		content.appendChild( vHeader );
		content.appendChild( vTable );
		
		// add event listener
		vBtnRegister.addEventListener( 'click', function(){
				var vRegister = new Register();
				vRegister.register();
			} );
		
		
	} else {
		console.error( "No valid element given for registration form." );
	}
	
}
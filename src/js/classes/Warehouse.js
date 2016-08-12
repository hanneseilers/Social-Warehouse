/**
 * Warehouse instance
 */
function Warehouse(id, name, description, country, city){
	// warehouse base information
	this.id = id;
	this.name = name;
	this.description = description;
	this.country = country;
	this.city = city;
	
	// details (only available if logged in)
	this.detailsLoaded = false;
	this.mail = null;
	this.disableLocationLess = false;
	this.disablePaletteLess = false
	
	// dom elements
	this.domPassword = null;
	this.domPasswordInput = null;
	
	// stock object
	this.stock = null;
	
	
	/**
	 * Shows the warehouse data
	 */
	this.showContent = function(content){		
		if( content ){
			// create content
			var html = "<div class='divreset'>"
				+ "<table class='table2 border'>"
				+ "<tr class='tr_top'>"
				
					+ "<td class='width25' >"	
					
						+ "<table>"						
							+ "<tr>"
							
								+ "<td class='table_header highlight'>"
									+ LANG('palettes')
								+ "</td>"
								
							+ "</tr>"
							+ "<tr>"
							
								+ "<td class='table_content' id='palettes'>"
									+ "<img src='img/loading.gif' /> " + LANG('loading')
								+ "</td>"	
								
							+ "</tr>"
							+ "<tr>"
							
								+ "<td class='table_header highlight'>"
									+ LANG('locations')
								+ "</td>"
								
							+ "</tr>"
							+ "<tr>"
							
								+ "<td class='table_content' id='locations'>"
									+ "<img src='img/loading.gif' /> " + LANG('loading')
								+ "</td>"
								
							+ "</tr>"							
						+ "</table>"
						
					+ "</td>"					
					+ "<td class='tr_top'>"
					
						+ "<table>"	
							+ "<tr>"
							
								+ "<td class='table_header highlight'>"
									+ LANG('stock')
								+ "</td>"
							
							+ "</tr>"
							+ "<tr>"
							
								+ "<td rowspan=3 class='table_content' id='stock'>"
									+ "<img src='img/loading.gif' /> " + LANG('loading')
								+ "</td>"
								
							+ "</tr>"							
						+ "</table>"
					+ "</td>"
					
				+ "</tr>"
				+ "</table>"
				+ "</div>";
			
			content.innerHTML = html;
			
			// load locations
			Location.listLocations( document.getElementById('locations') );
			
			// load palettes
			Palette.listPalettes( document.getElementById('palettes') );
			
			// load stock
			this.stock = new Stock();
			document.getElementById('stock').innerHTML = "";
			document.getElementById('stock').appendChild( this.stock.getDOMElement() );
			
			// scroll to menu
			$.scrollTo( document.getElementById('menu') );
		}		
	}
	
	/**
	 * Shows warehouse element for login page.
	 * @param content	DOM element where to add the warehouse.
	 */
	this.show = function(content){
		// create base elements
		var group = document.createElement( 'div' );
		var left = document.createElement( 'span' );
		
		// set classes
		group.className = 'groupitem smalltext';
		left.className = 'group_left boldtextr';
		
		// set content
		left.innerHTML = this.country +", "+ this.city +": "+ this.name;
		if( this.detailsLoaded ){
			left.innerHTML = left.innerHTML
				+ " (" + (Main.getInstance().session.restricted ? LANG('access_restricted') : LANG('access_admin')) + ")";
		}
		
		// add base elements
		group.appendChild( left );
	
		if( this.detailsLoaded ){
			group.appendChild( this.__getMenuContent() );
		} else { 
			group.appendChild( this.__getListContent() );
		}
		
		// add warehouse content
		content.appendChild( group );
	}
	
	/**
	 * @returns	DOM element with content for menu (edit, demand, logout button):
	 */
	this.__getMenuContent = function(){
		var group = document.createElement( 'span' );
		var btnEdit = document.createElement( 'a' );
		var btnDemand = document.createElement( 'a' );
		var btnLogout = document.createElement( 'a' );
		
		// set classes
		group.className = "group_right tinytext";
		btnDemand.className = 'button table_cell yellow';
		btnEdit.className = 'button table_cell';
		btnLogout.className = 'button table_cell lightred';
		
		// set content		
		btnDemand.innerHTML = "<img src='img/action/demand.png' />";
		btnEdit.innerHTML = "<img src='img/action/edit.png' />";
		btnLogout.innerHTML = "<img src='img/action/login.png' /> " + LANG('logout');
		
		// add event listener
		var self = this;
		btnLogout.addEventListener( 'click', function(){ self.logout(); } );
		btnDemand.addEventListener( 'click', function(){ console.debug('show demand'); } );
		btnEdit.addEventListener( 'click', function(){ console.debug('edit'); } );
		
		// append elements
		if( !Main.getInstance().session.restricted )
			group.appendChild( btnEdit );
		group.appendChild( btnDemand );
		group.appendChild( btnLogout );
		
		return group;
	}
	
	/**
	 * @returns	DOM element with content for list of warehouses (info, demand and login button).
	 */
	this.__getListContent = function(){
		var group = document.createElement( 'span' );
		this.domPassword = document.createElement( 'span' );
		this.domPasswordInput = document.createElement( 'input' );
		var btnDemand = document.createElement( 'a' );
		var btnInfo = document.createElement( 'a' );
		var btnLogin = document.createElement( 'a' );
		
		// set classes
		
		this.domPassword.className = 'inline_text table_cell hidetext';
		btnDemand.className = 'button table_cell yellow';
		btnInfo.className = 'button table_cell';
		btnLogin.className = 'button table_cell green';
		
		// set content	
		group.className = "group_right";
		this.domPassword.innerHTML = LANG('password') + ": ";
		this.domPasswordInput.type = 'password';
		btnDemand.innerHTML = "<img src='img/action/demand.png' />";
		btnInfo.innerHTML = "<img src='img/action/about.png' />";
		btnLogin.innerHTML = "<img src='img/action/login.png' /> " + LANG('login');
		
		// add event listener
		var self = this;
		btnLogin.addEventListener( 'click', function(){ self.login(); } );
		btnDemand.addEventListener( 'click', function(){ console.debug('show demand'); } );
		btnInfo.addEventListener( 'click', function(){ self.showInfo(); } );
		this.domPasswordInput.addEventListener( 'keypress', function(event){ if(event.keyCode == 13) self.login(); } );
		
		// append elements
		this.domPassword.appendChild( this.domPasswordInput );
		group.appendChild( this.domPassword );
		group.appendChild( btnInfo );
		group.appendChild( btnDemand );
		group.appendChild( btnLogin );
		
		return group;
	}
	
	/**
	 * Loads warehouse data from server
	 */
	this.load = function(callback, update){
		var self = this;
		get( 'getWarehouse', {'sessionId': Main.getInstance().session.id, 'update': update}, function(data){
			if( data && data.response ){
				data = data.response;
				
				// set data
				self.id = data.id;
				self.name = data.name;
				self.description = data.description;
				self.country = data.country;
				self.city = data.city;
				
				self.mail = data.mail
				self.disableLocationLess = data.disableLocationLess;
				self.disablePaletteLess = data.disablePaletteLess;
				
				self.detailsLoaded = true;
				
				// load locations
				Location.load( function(){
					
					// load palettes
					Palette.load( function(){
						
						Category.load( function(){
							
							// check callback
							if( callback )
								callback();
							
						});
						
					} );
					
				} );
				
				
			} else {
				self.detailsLoaded = false;
				console.error( 'Cannot receive warehouse data.' );
			}
		} );
		
	}
	
	/**
	 * Logs in
	 */
	this.login = function(){
		if( this.domPassword){
			
			if( this.domPassword.style.display == 'table-cell' ){
				
				// hide login
				this.domPassword.style.display = 'none';
				
				// try to login
				Main.getInstance().login( null, id, this.domPasswordInput.value );
				
			} else {
				
				// show login
				this.domPassword.style.display = 'table-cell';
				this.domPasswordInput.focus();
				
			}
			
		}
	}
	
	/**
	 * Logs out
	 */
	this.logout = function(){
		get( 'logout', {}, function(data){
			if( data ){
				Main.getInstance().barcodescanner.unregister();
				Session.reset();
				location.reload();
			} else {
				concole.log( 'Cannot log out' );
			}
		} );
	}
	
	/**
	 * Shows description info.
	 */
	this.showInfo = function(){		
		var content = "no description";
		if( this.description )
			content = this.description;
		
		var overlay = Overlay.getOverlay( content, LANG('close'), null, function(){ overlay.hide(); }, null );
		overlay.show();
	}

}

/**
 * Shows warehouse list
 * @param content	Element where to show list
 */
Warehouse.listWarehouses = function(content){
	
	get( 'getWarehouses', {}, function(data){
		if( data ){
			var vHeader = document.createElement( 'h1' );
			vHeader.innerHTML = LANG('warehouses') + ":";
			content.appendChild( vHeader );
			
			for( var i=0; i<data.response.length; i++ ){
				var vData = data.response[i];
				var warehouse = new Warehouse( vData['id'],vData['name'], vData['description'], vData['country'], vData['city'] );
				warehouse.show( content ); 
			}
		}
		
		Main.getInstance().showLoading( false );
	} );
	
}

/**
 * Shows a warehouse login input
 * @param id	Warehouse ID
 */
Warehouse.showLoginInput = function(id){
	var loginInput = document.getElementById( 'warehousePw_'+id );
	if( loginInput.parentElement.style.display == 'table-cell' ){
		loginInput.parentElement.style.display = 'none';
		document.getElementById( 'warehouseLoginFailed_'+id ).style.display = 'none';
		
		// login
		Main.getInstance().login( null, id );
		
	} else {
		loginInput.parentElement.style.display = 'table-cell';
		loginInput.focus();
	}
}
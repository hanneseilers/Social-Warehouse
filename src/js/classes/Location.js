/**
 * Location Class
 */
function Location(id, name, warehouseId){
	this.id = id;
	this.name = name;
	this.warehouseId = warehouseId;
	
	this.domElement = null;
	this.domName = null;
	this.overlay = null;
	
	this.selected = false;
	
	/**
	 * Adds this location to a content
	 * @param content	DOM element
	 */
	this.getDOMElement = function(){
		if( this.domElement == null ){
			
			// create elements
			this.domElement = document.createElement( 'div' );
			this.domName = document.createElement( 'span' );
			var btnEdit = document.createElement( 'a' );
			var btnDelete = document.createElement( 'a' );
			var btnPrint = document.createElement( 'a' );
			var btnStock = document.createElement( 'a' );
			
			// add event listener
			var self = this;
			btnEdit.addEventListener( 'click', function(){ self.edit(); } );
			btnDelete.addEventListener( 'click', function(){ self.discard(); } );
			btnPrint.addEventListener( 'click', function(){ self.print(); } );
			btnStock.addEventListener( 'click', function(){ self.showStock(); } );
			this.domName.addEventListener( 'click', function(){ self.select(); } )
			
			// ad classes
			this.domElement.className = 'groupitem';
			this.domName.className = 'group_left' + (this.selected ? ' yellow' : '');
			btnEdit.className = 'button';
			btnPrint.className = 'button';
			btnStock.className = 'button';
			btnDelete.className = 'button red';
			
			// add content
			btnEdit.id = 'location_edit_' + this.id;
			btnDelete.id = 'location_delete_' + this.id;
			btnPrint.id = 'location_print' + this.id;
			btnStock.id = 'location_stock' + this.id;
			
			this.domName.innerHTML = this.name;
			btnEdit.innerHTML = "<img src='img/action/edit.png' />";
			btnDelete.innerHTML = "<img src='img/action/discard.png' />";
			btnPrint.innerHTML = "<img src='img/action/print.png' />";
			btnStock.innerHTML = "<img src='img/action/about.png' />";
			
			// append to document
			this.domElement.appendChild( this.domName );
			this.domElement.appendChild( btnStock );
			if( !Main.getInstance().session.restricted ){
				this.domElement.appendChild( btnEdit );
				this.domElement.appendChild( btnPrint );
				this.domElement.appendChild( btnDelete );
			}
			
		}
		
		return this.domElement;
	}
	
	/**
	 * Selects / Unselects location
	 * @param select	Set if to select location. If not set (null) selection is toggled.
	 */
	this.select = function(select){
		// unhighlight all other locations
		for( var i=0; i<Location.locations.length; i++ ){
			if( Location.locations[i] != this ){
				Location.locations[i].selected = false;
				if( Location.locations[i].domElement )
					Location.locations[i].domElement.className = Location.locations[i].domElement.className.replace( ' yellow', '' );
			}
		}
		
		// set selected
		if( select == null || select == undefined )
			select = !this.selected;
		this.selected = select;
		
		// highlight this location		
		if( this.domElement ){
			this.domElement.className = this.domElement.className.replace( ' yellow', '' );
			
			if( this.selected )
				this.domElement.className = this.domElement.className + ' yellow';
		}
	}
	
	/**
	 * Edits location
	 */
	this.edit = function(){
		if( this.domName && this.domName.childNodes.length > 0 ){
			if( this.domName.childNodes[0].nodeType == 3 ){
				// replace by input
				this.domName.innerHTML = "<input type='name' value='" + this.name + "' />";
				
			} else {
				// get value and save
				var name = this.domName.childNodes[0].value;
				if( name ){
					this.name = name;
					this._update();
				}
			}
		}
	}
	
	/**
	 * Deletes location
	 */
	this.discard = function(){
		// create dom element
		var dom = document.createElement( 'div' );
		dom.style.fontSize = "large";
		dom.innerHTML = LANG( 'location_delete_question' ).replace( '%', this.name );
		
		// create overlay
		var self = this;
		var overlay = new Overlay( dom, LANG('delete'), LANG('cancel'),
			function(){
			
				dom.innerHTML = "<img src='img/loading.gif' /> " + LANG('loading');
				get( 'deleteLocation', {'id': self.id}, function(data){
					if( data && data.response ){
						
						// reload location
						var element = document.getElementById('locations');
						element.innerHTML = "<img src='img/loading.gif' /> " + LANG('loading');
						Location.load( function(){ Location.listLocations( element ); } );
						showStatusMessage( LANG('location_deleted') );						
						
					} else {
						showErrorMessage( LANG('location_delete_failed') );
					}
					overlay.hide();
				} );	
				
			},
			function(){ overlay.hide() } );
		overlay.show();
	}
	
	/**
	 * Prints a barcode for this location
	 */
	this.print = function(){
		console.log("print");
		get( 'getBarcodeUri', {'text': "SWL"+this.id+"SW"}, function(data){
			if( data && data.response ){
				
				var doc = new jsPDF( 'l', 'mm', [62, 28.9] );
				doc.addImage( data.response, 'png', 1, 1, 60, 27 );
				doc.output('dataurlnewwindow');
				
			} else {
				showErrorMessage( LANG('barcode_failed') );
			}
		} );
	}
	
	/**
	 * Shows carton stock in overlay
	 */
	this.showStock = function(){
		var dom = document.createElement( 'div' );
		dom.innerHTML = "<img src='img/loading.gif' /> " + LANG('loading');
		
		// show overlay
		var overlay = new Overlay( dom, LANG('close'), null, function(){ overlay.hide(); }, null );
		overlay.show();
		
		// load stock data
		get( 'getStock', {'location': this.id}, function(data){
			if( data && data.response ){
				Stock.showStock( data.response, dom );
			} else {
				dom.innerHTML = LANG('stock_no_data');
				dom.className = 'errortext';
			}			
		} );
	}
	
	/**
	 * Hides overlay and sets it to null
	 */
	this.resetOverlay = function(){
		if( this.overlay ){
			this.overlay.hide();
			this.overlay = null;
		}
	}
	
	/**
	 * Updates DOM element by replacing it with a new one.
	 */
	this._updateDOMElement = function(){
		if( this.domElement ){
			var oldDom = this.domElement;
			this.domElement = null;
			
			oldDom.parentNode.replaceChild( this.getDOMElement(), oldDom );
		}
	}
	

	/**
	 * Updates location in database
	 */
	this._update = function(){
		var self = this;
		get( 'editLocation', {'id': this.id, 'name': this.name}, function(data){
			if( data && data.response ){
				
				Location.load( function(){
					self._updateDOMElement();
					showStatusMessage( LANG('location_updated') );
				} );				
				
			} else {
				showErrorMessage( LANG('location_update_failed') );
			}
		} );
	}
}

// List of available locations
Location.locations = [];

/**
 * Loads locations
 * @param callback	Callback function to call after locations are loaded.
 */
Location.load = function(callback){
	
	get( 'getLocations', {}, function(data){
		if(data.response){			
			data = data.response;			
				
			// add locations
			Location.locations = [];
			for( var i=0; i < data.length; i++ ){
				var location = new Location( data[i]['id'], data[i]['name'], data[i]['warehouseId'] );
				Location.locations.push( location );
			}
			
		} else {
			console.error( 'Cannot load locations' );
		}
		
		if( callback );
			callback();
	} );
	
}

/**
 * Lists locations on DOM element.
 * @param content	DOM element
 */
Location.listLocations = function(content){
	
	if( content ){
	
			// clear content
			content.innerHTML = "";
			
			// create elements
			var containerAdd = document.createElement( 'div' );
			var leftAdd = document.createElement( 'span' );
			var inpAdd = document.createElement( 'input' );
			var btnAdd = document.createElement( 'a' );
			var spacer = document.createElement( 'div' );
			inpAdd.id = 'inpAddLocation';
			
			// add classes
			leftAdd.className = 'group_left';
			btnAdd.className = 'button table_cell green';
			spacer.className = 'hspacer';
			
			// add content
			leftAdd.innerHTML = LANG('location_add') + ": ";
			btnAdd.innerHTML = "<img src='img/action/add.png' />";
			
			// add listener
			btnAdd.addEventListener( 'click', Location.addLocation );
			inpAdd.addEventListener( 'keyup', function(e){
				if( Main.getInstance().barcodescanner.state == 0
						&& ((window.event && e.keyCode == 13 ) || e.which == 13) ){
					Location.addLocation();
				}
			} )
			
			// add elements
			containerAdd.appendChild( leftAdd );
			leftAdd.appendChild( inpAdd );
			containerAdd.appendChild( btnAdd );
			
			if( !Main.getInstance().session.restricted ){
				content.appendChild( containerAdd );
				content.appendChild( spacer );
			}
			
			// add locations
			for( var i=0; i < Location.locations.length; i++ ){
				content.appendChild( Location.locations[i].getDOMElement() );
			}
			
	} else {
		console.error( "Cannot show locations on null content" );
	}
	
}

/**
 * Gets location by id
 * @param id	ID of location
 * @returns 	Location or null if not found
 */
Location.getLocation = function(id){
	for( var i=0; i<Location.locations.length; i++ ){
		if( Location.locations[i].id == id )
			return Location.locations[i];
	}
	
	return null;
}


/**
 * Adds a new location
 */
Location.addLocation = function(){
	var element = document.getElementById( 'inpAddLocation' );
	get( 'addLocation', {'name': element.value}, function(data){
		if( data && data.response ){
			
			// reload and show location
			showStatusMessage( LANG('location_added') );
			var element = document.getElementById('locations');
			element.innerHTML = "<img src='img/loading.gif' /> " + LANG('loading');
			Location.load( function(){ Location.listLocations( element ); } );
			
		} else {
			showErrorMessage( LANG('location_add_failed') );
		}
	} );
}

/**
 * Gets selected location
 * @returns Selected location or null if none selected.
 */
Location.getSelected = function(){
	for( var i=0; i<Location.locations.length; i++ ){
		if( Location.locations[i].selected )
			return Location.locations[i];
	}
	
	return null;
}
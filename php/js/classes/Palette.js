/**
 * Palette instance
 */
function Palette(id, number, locationId, warehouseId){
	this.id = id;
	this.number = number;
	this.locationId = locationId;
	this.warehouseId = warehouseId;
	
	this.selected = false;
	this.domElement = null;
	
	/**
	 * @returns DOM element of palette
	 */
	this.getDOMElement = function(){
		
		if( this.domElement == null ){
			// get corresponding location
			var location = Location.getLocation( this.locationId );
			var locationSelected = Location.getSelected();
			
			// create elements
			this.domElement = document.createElement( 'div' );
			var left = document.createElement( 'span' );
			var btnPrint = document.createElement( 'a' );
			var btnDiscard = document.createElement( 'a' );
			var btnMove = document.createElement( 'a' );
			var btnStock = document.createElement( 'a' );
			
			// add classes
			this.domElement.className = 'groupitem' + (this.selected ? ' yellow' : '');
			left.className = 'group_left';
			btnPrint.className = 'button table_cell';
			btnMove.className = 'button table_cell';
			btnDiscard.className = 'button table_cell red';
			btnStock.className = 'button';
			
			// add content
			left.innerHTML = "#" + this.number + (location ? " : "+location.name : "");
			btnPrint.innerHTML = "<img src='img/action/print.png' />";
			btnMove.innerHTML = "<img src='img/action/move.png' />";
			btnDiscard.innerHTML = "<img src='img/action/discard.png' />";
			btnStock.innerHTML = "<img src='img/action/about.png' />";
			
			btnPrint.title = LANG( 'palette_btn_print_tooltip' );
			btnMove.title =  LANG( 'palette_btn_move_tooltip' );
			btnDiscard.title = LANG( 'btn_delete_tooltip' );
			
			// add listener
			var self = this;
			left.addEventListener( 'click', function(){ self.select(); } );
			btnPrint.addEventListener( 'click', function(){ self.print(); } );
			btnMove.addEventListener( 'click', function(){ self.move(); } )
			btnDiscard.addEventListener( 'click', function(){ self.discard(); } );
			btnStock.addEventListener( 'click', function(){ self.showStock(); } );
			
			// add elements
			this.domElement.appendChild( left );
			this.domElement.appendChild( btnPrint );
			this.domElement.appendChild( btnMove );
			this.domElement.appendChild( btnStock );
			this.domElement.appendChild( btnDiscard );
			
		}
		
		return this.domElement;
		
	}
	
	/**
	 * Selects / Unselects palette
	 * @param select	Set if to select palette. If not set (null) selection is toggled.
	 */
	this.select = function(select){
		if( select == null )
			select = !this.selected;
				
		this.selected = select;
		
		if( this.domElement ){
			this.domElement.className = this.domElement.className.replace( ' yellow', '' );
			
			if( this.selected ){
				this.domElement.className = this.domElement.className + ' yellow';
				
				if( !Palette.multiselect ){
					
					// deselect all other palettes
					for( var i=0; i<Palette.palettes.length; i++ ){
						if( Palette.palettes[i].id != this.id )
							Palette.palettes[i].select( false );
					}
					
					// check if to update location
					var location = Location.getLocation( this.locationId );
					if( location )
						location.select( true );
					
				}
				
			}
		}
	}
	
	/**
	 * Shows palette paper for print
	 */
	this.print = function(){
		var self = this;
		get( 'getBarcodeUri', {
			'text': "SWP"+this.id+"SW",
			'barThick': 4,
			'barThin': 2,
			}, function(data){
			if( data && data.response ){
				
				var warehouse = Main.getInstance().warehouse;
				var doc = new jsPDF( 'p', 'mm' );
				doc.text( 20, 20, warehouse.country
						+ ", " + warehouse.city
						+ ": " + warehouse.name
						);
				doc.addImage( data.response, 'png', 20, 30, 170, 80 );
				doc.setFontSize( 50 );
				doc.setFontType( 'bold' );
				doc.text( 50, 130, LANG('palette') + " #" + self.id );
				doc.output('dataurlnewwindow');
				
			} else {
				showErrorMessage( LANG('barcode_failed') );
			}
		} );
	}
	
	/**
	 * Deletes palette
	 */
	this.discard = function(){
		var dom = this.domElement;
		dom.innerHTML = "<img src='img/loading.gif' /> " + LANG('loading');
		Palette.resetSelections();
		get( 'deletePalette', {'id': this.id}, function(data){
			if( data.response ){
				Palette.load( function(){
					Palette.showPalettesInSideList( [] );
					Palette.updateTotalNumberOfPalettes();
					showStatusMessage( LANG('palette_deleted') );
				} );
			} else {
				showErrorMessage( LANG('palette_delete_failed') );
			}
			
			dom.parentElement.removeChild( dom );
		} );
	}
	
	/**
	 * Moves palette to new location
	 */
	this.move = function(){
		var location = Location.getSelected();
		
		if( location ){
			// move palette to new location
			this.locationId = location.id
			
		} else {
			// remove location
			this.locationId = null;
		}
		
		this._update();
		
	}
	
	/**
	 * Shows stock on palette
	 */
	this.showStock = function(){
		var dom = document.createElement( 'div' );
		dom.innerHTML = "<img src='img/loading.gif' /> " + LANG('loading');
		
		// show overlay
		var overlay = new Overlay( dom, LANG('close'), null, function(){ overlay.hide(); }, null );
		overlay.show();
		
		// load stock data
		get( 'getStock', {'palette': this.id}, function(data){
			if( data && data.response ){
				Stock.showStock( data.response, dom );				
			} else {
				dom.innerHTML = LANG('stock_no_data');
				dom.className = 'errortext';
			}			
		} );
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
	 * Updates palette in database
	 */
	this._update = function(){
		var self = this;
		get( 'editPalette', {'id': this.id, 'locationId': this.locationId}, function(data){
			if( data && data.response ){
				
				Palette.load( function(){
					self._updateDOMElement();
					showStatusMessage( LANG('palette_updated') );
					
					// update carton if active
					if( Carton.selected )
						Carton.load( Carton.selected.id );
					
				} );				
				
			} else {
				showErrorMessage( LANG('palette_update_failed') );
			}
		} );
	}
	
}

// List of available palettes
Palette.palettes = [];

// List of DOM elements with total number of palettes
Palette.palettesTotalDOM = [];

// Enables / Disables palette multiselect
Palette.multiselect = false;

/**
 * Loads palettes
 * @param callback	Callbakc function to call after data was loaded.
 */
Palette.load = function(callback){
	get( 'getPalettes', {'sessionId': Main.getInstance().session.id}, function(data){		
		if( data.response ){
			var palettes = data.response;
			
			// add palettes
			Palette.palettes = [];
			for( var i=0; i<palettes.length; i++ ){
				var palette = new Palette( palettes[i]['id'], palettes[i]['number'], palettes[i]['locationId'], palettes[i]['warehouseId'] );
				Palette.palettes.push( palette );
			}
		}
		
		if( callback )
			callback();
			
	} );
}

/**
 * Gets a palette by id.
 * @param id	ID of palette
 */
Palette.getPalette = function(id){
	for( var i=0; i < Palette.palettes.length; i++ ){
		if( Palette.palettes[i].id == id )
			return Palette.palettes[i];
	}
	
	return null;
}

/**
 * Resets all palettes to not selected.
 */
Palette.resetSelections = function(){
	for( var i=0; i < Palette.palettes.length; i++ ){
		Palette.palettes[i].select( false );
	}
}

/**
 * Lists palettes on DOM element.
 * @param content	DOM element
 */
Palette.listPalettes = function(content){
	
	// clear content
	content.innerHTML = "";
	
	// create elements
	var containerSearch = document.createElement( 'div' );
	var leftSearch = document.createElement( 'span' );
	var inpSearch = document.createElement( 'input' );
	var btnSearch = document.createElement( 'a' );
	var spacer = document.createElement( 'div' );
	var selection = document.createElement( 'span' );
	var containerTotal = document.createElement( 'div' );
	var total = document.createElement( 'span' );
	var btnViewPalettes = document.createElement( 'a' );
	inpSearch.type = 'number';
	inpSearch.id = 'paletteSearch';
	selection.id = 'paletteSelected';
	
	var btnAdd = document.createElement( 'a' );
	
	// add classes
	leftSearch.className = 'group_left';
	btnSearch.className = 'button table_cell';
	btnAdd.className = 'button table_cell green';
	btnViewPalettes.className = 'button table_cell yellow';
	spacer.className = 'hspacer';
	selection.className = 'errortext';
	total.className = 'group_left';
	
	// add content
	leftSearch.innerHTML = LANG('palette') + ": ";
	btnSearch.innerHTML = "<img src='img/action/search.png' />";
	btnAdd.innerHTML = "<img src='img/action/add.png' />";
	btnViewPalettes.innerHTML = "<img src='img/action/list.png' />";
	
	// add listener
	btnSearch.addEventListener( 'click', Palette.searchPalette );
	btnAdd.addEventListener( 'click', Palette.addPalette )
	btnViewPalettes.addEventListener( 'click', Palette.viewPalettes );
	inpSearch.addEventListener( 'keyup', Palette.searchPalette );
	
	// add elements
	containerSearch.appendChild( leftSearch );			
	leftSearch.appendChild( inpSearch );
	containerSearch.appendChild( btnSearch );	
	containerSearch.appendChild( btnAdd );
	
	containerTotal.appendChild( total );
	//containerTotal.appendChild( btnViewPalettes );
	
	content.appendChild( containerSearch );			
	content.appendChild( spacer );
	content.appendChild( selection );
	content.appendChild( containerTotal );
	
	// show total number of palettes
	Palette.palettesTotalDOM.push( total );
	Palette.updateTotalNumberOfPalettes();
	
	// show initial selection text
	Palette.resetPalettesSelected();
	
}

/**
 * Updates all dom element where total number of palettes are shown.
 */
Palette.updateTotalNumberOfPalettes = function(){
	for( var i=0; i<Palette.palettesTotalDOM.length; i++ ){
		Palette.palettesTotalDOM[i].innerHTML = LANG('palettes_total') + ": " + Palette.palettes.length;
	}
}

/**
 * Searches for a palette
 */
Palette.paletteSearchFlag = false;
Palette.searchPalette = function(){
	var element = document.getElementById( 'paletteSearch' );
	
	// check if search input field was found
	if( element ){
		var number = parseInt(element.value);
		
		// reset selected palettes
		Palette.resetSelections();
		
		// reset selected location
		var location = Location.getSelected();
		if( location )
			location.select( false );
		
		// check if number was entered
		if( number ){
			
			// check flag and stop running searches
			if( Palette.paletteSearchFlag )
				Palette.paletteSearchFlag = false;
			
			// set flag
			Palette.paletteSearchFlag = true;
			
			// search for suitable palettes
			var palettes = [];
			for( var i=0; Palette.paletteSearchFlag && i<Palette.palettes.length; i++ ){
				
				// check if to exit
				if( !Palette.paletteSearchFlag )
					return;
				
				// check if palette matches
				var refNumber = Palette.palettes[i].number;
				if( refNumber == number )
					palettes.push( Palette.palettes[i] )
			}
			
			// show suitable palettes
			Palette.showPalettesInSideList( palettes );
			
		} else {
			Palette.resetPalettesSelected();
		}
	}
}

/**
 * Shows palettes in palette side list
 */
Palette.showPalettesInSideList = function(palettes){
	var container = document.getElementById( 'paletteSelected' );
	if( container ){				
		container.innerHTML = "";
		container.className = "";
		
		for( var i=0; i<palettes.length; i++ ){
			var dom = palettes[i].getDOMElement();
			container.appendChild( dom );
		}
		
		// check if palette found
		if( palettes.length == 0 )
			Palette.resetPalettesSelected( LANG('palette_not_found') );
		
		// check if to select first palette
		// if only one found
		if( palettes.length == 1 )
			palettes[0].select();
		
	} else {
		console.error( 'Cannot find container element to show palettes' )
	}
}

/**
 * Resets palettes selected element to a error text.
 * @param text	Optional text to use insteadt of palette_none_selected
 */
Palette.resetPalettesSelected = function(text){
	// get container element
	var container = document.getElementById( 'paletteSelected' );
	if( container ){		
		
		// check text
		if( typeof text != 'string' )
			text = LANG('palette_none_selected');
		
		// set text
		container.innerHTML = "<div>" + text + "</div>";
		container.className = "errortext";
		
	}
}

/**
 * Adds some new palettes
 */
Palette.addPalette = function(){
	var location = Location.getSelected();
	var data = {}; 
	if( location )
		data = {'locationId': location.id};
	
	Palette.resetPalettesSelected( "<img src='img/loading.gif' /> " + LANG('loading') );
	
	get( 'addPalette', data, function( data ){
		if( data && data.response ){
			Palette.load( function(){
				Palette.updateTotalNumberOfPalettes();
				Palette.resetPalettesSelected();
				var palette = Palette.getPalette( data.response );
				if( palette ){
					Palette.showPalettesInSideList( [palette] );
					palette.select( true );
				}
				showStatusMessage( LANG('palette_added') );
			} );
		} else {
			showErrorMessage( LANG('palette_add_failed') );
		}
	} );
}

/**
 * Gets selected palettes
 * @return Array of selected palettes.
 */
Palette.getSelected = function(){
	var palettes = [];
	for( var i=0; i<Palette.palettes.length; i++ ){
		if( Palette.palettes[i].selected )
			palettes.push( Palette.palettes[i] );
	}
	
	return palettes;
}

/**
 * Shows a list of palettes
 */
Palette.viewPalettes = function(){
	
	Palette.multiselect = true;
	
	// create content container
	var content = document.createElement( 'div' );
	
	// add palettes
	for( var i=0; i<Palette.palettes.length; i++ ){
		content.appendChild( Palette.palettes[i].getDOMElement() );
	}
	
	// create print button
	var btnPrintAll = document.createElement( 'a' );
	btnPrintAll.className = 'button block';
	btnPrintAll.addEventListener( 'click', function(){
		console.debug('print all');
		// TODO: print all selected palettes
	} );
	btnPrintAll.innerHTML = "<img src='img/action/print.png' /> " + LANG('palettes_print_selected');
	content.appendChild( btnPrintAll );	
	
	// show overlay
	var overlay = Overlay.getOverlay( content, LANG('close'), null, function(){
		// deselect all palettes
		Palette.resetSelections();
		Palette.multiselect = false;
		
		// hide overlay
		overlay.hide();
	}, null );
	overlay.show();
}
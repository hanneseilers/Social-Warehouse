/**
 * Carton class
 */
function Carton(id, paletteId, locationId){
	this.id = id;
	this.paletteId = paletteId;
	this.locationId = locationId;
	
	/**
	 * Updates carton data
	 */
	this.edit = function(){
		get( 'editCarton', {
			'id': this.id,
			'location': this.locationId,
			'palette': this.paletteId,
			}, function(data){
				if( data && data.response )
					showStatusMessage( LANG('carton_edited') );
				else
					showStatusMessage( LANG('carton_edit_failed') );
					
				if( Carton.selected )
					Carton.load( Carton.selected.id );
		} );
	}
	
	/**
	 * Deletes carton.
	 */
	this.discard = function(){
		get( 'deleteCarton', {'id': this.id}, function(data){
			if( data && data.response ){
				showStatusMessage( LANG('carton_deleted') );
				Carton.load( -1 );
				Carton.selected = null;
			} else {
				showErrorMessage( LANG('carton_delete_failed') );
			}
		} );
	}
	
	/**
	 * Tries to create this carton
	 * @param callback Callback function to call after carton was created
	 */
	this.create = function(callback){
		if( this.id == null ){
			
			get( 'addCarton', {
				'location': this.locationId,
				'palette': this.paletteId
			},
			function(data){
				if( data && !isNaN(data.response) ){
					Carton.load( data.response );
					showStatusMessage( LANG('category_added') );
				} else {
					showErrorMessage( LANG('carton_add_failed') );
				}
			} );
			
			return true;
			
		}
		return false;
	}
	
	/**
	 * Prints carton barcode and shows it
	 */
	this.print = function(){
		get( 'getBarcodeUri', {'text': "SWC"+this.id+"SW"}, function(data){
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
	this.showStock = function(showOutgo=false){
		var dom = document.createElement( 'div' );
		dom.innerHTML = "<img src='img/loading.gif' /> " + LANG('loading');
		
		// show overlay
		var overlay = new Overlay( dom, LANG('close'), null, function(){ overlay.hide(); }, null );
		overlay.show();
		
		// load stock data
		get( 'getStock', {'carton': this.id}, function(data){
			if( data && data.response ){
				Stock.showStock( data.response, dom, showOutgo );				
			} else {
				dom.innerHTML = LANG('stock_no_data');
				dom.className = 'errortext';
			}
		} );
	}
		
}

// Selected carton
Carton.selected = null;

/**
 * Loads a carton and shows it.
 * @param id	ID of carton to load
 */
Carton.load = function(id){	
	var element = document.getElementById( 'cartonInfo' );
	
	if( id > 0 ){
		get( 'getCarton', {'id': id}, function(data){
			if( data && data.response && data.response.id > 0 ){			
				var carton = new Carton( data.response.id, data.response.paletteId, data.response.locationId );
				var location = Location.getLocation( carton.locationId );
				var palette = Palette.getPalette( carton.paletteId );
				Carton.selected = carton;
				
				// select corresponding location and palette
				var palette = Palette.getPalette( carton.paletteId );			
				if( palette ){
					Palette.showPalettesInSideList( [palette] );		// show palette and its location
					palette.select( true );
				} else if ( location ){
					location.select( true );							// show only location (no palette)
				} else {
					location = Location.getSelected();
					if( location )
						location.select( false );						// deactivate location (carton has none)
				}
				
				element.innerHTML = LANG( 'carton' ) + " #" + carton.id + "<br />"
					+ "" + LANG( 'location' ) + " " + ( location ? location.name : undefined )
					+ ", " + LANG( 'palette' ) + (palette ? " #" + palette.number : " " + undefined);
				element.className = "table_cell inline_text";
				Carton.showButtons(true);
					
			} else {
				element.innerHTML = LANG( 'carton_none_selected' );
				element.className = 'table_cell inline_text errortext';
				showErrorMessage( LANG('carton_load_failed') );
				Carton.showButtons(false);
			}
		} );
	} else {
		element.innerHTML = LANG( 'carton_none_selected' );
		element.className = 'table_cell inline_text errortext';
		Carton.selected = null;
		Carton.showButtons(false);
		Palette.resetSelections();
	}
	
}

Carton.showButtons = function(show){
	if( show ){
		document.getElementById( 'btnCartonEdit' ).style.display = 'table-cell';
		document.getElementById( 'btnCartonPrint' ).style.display = 'table-cell';
		document.getElementById( 'btnCartonDiscard' ).style.display = 'table-cell';
		document.getElementById( 'btnCartonStockIncome' ).style.display = 'table-cell';
		document.getElementById( 'btnCartonStockOutgo' ).style.display = 'table-cell';
	} else {
		document.getElementById( 'btnCartonEdit' ).style.display = 'none';
		document.getElementById( 'btnCartonPrint' ).style.display = 'none';
		document.getElementById( 'btnCartonDiscard' ).style.display = 'none';
		document.getElementById( 'btnCartonStockIncome' ).style.display = 'none';
		document.getElementById( 'btnCartonStockOutgo' ).style.display = 'none';
	}
}

/**
 * Searches for a carton.
 */
Carton.search = function(){
	var id = parseInt( document.getElementById( 'cartonSearch' ).value );
	Carton.load( id );
	document.getElementById( 'cartonSearch' ).value = '';
}

/**
 * Adds a new carton and loads it.
 */
Carton.add = function(){
	// get selected palette & location
	var palette = null;
	if( Palette.getSelected().length > 0 )
		palette = Palette.getSelected()[0].id;
	
	var location = null;
	if( Location.getSelected() )
		location = Location.getSelected().id;
	
	// create new carton
	var carton = new Carton( null, palette, location );
	if( !carton.create() )
		showErrorMessage( LANG('carton_add_failed') );
}

/**
 * Edits selected carton.
 */
Carton.edit = function(){
	if( Carton.selected ){
		
		// get selected palette & location
		var palette = null;
		if( Palette.getSelected().length > 0 )
			palette = Palette.getSelected()[0].id;
		
		var location = null;
		if( Location.getSelected() )
			location = Location.getSelected().id;
		
		Carton.selected.locationId = location;
		Carton.selected.paletteId = palette;
		Carton.selected.edit();
		
	} else {
		showErrorMessage( LANG('carton_none_selected') );
	}
}

/**
 * Deletes selected carton.
 */
Carton.discard = function(){
	if( Carton.selected )
		Carton.selected.discard();
	else
		showErrorMessage( LANG('carton_none_selected') );
}

/**
 * Shows printable barcode of selected carton
 */
Carton.print = function(){
	if( Carton.selected )
		Carton.selected.print();
	else
		showErrorMessage( LANG('carton_none_selected') );
}

Carton.showStockIncome = function(){
	Carton.showStock(false);
}

Carton.showStockOutgo = function(){
	Carton.showStock(true);
}

/**
 * Shows stock information of selected carton
 */
Carton.showStock = function(showOutgo=false){
	if( Carton.selected )
		Carton.selected.showStock(showOutgo);
	else
		showErrorMessage( LANG('carton_none_selected') );
}

/**
 * Initiates DOM elements for editing carton data.
 * @param domCarton		Carton search and info DOM element
 * @param domStockForm	Stock edit form DOM element
 */
Carton.initDom = function(domCarton, domStockForm){
	
	// carton search and info
	var divCartonSearch = document.createElement( 'div' );
	var inpCartonSearch = document.createElement( 'input' );
	var divCartonInfo = document.createElement( 'span' );
	var btnCartonAdd = document.createElement( 'a' );
	var btnCartonEdit = document.createElement( 'a' );
	var btnCartonDiscard = document.createElement( 'a' );
	var btnCartonPrint = document.createElement( 'a' );
	var btnCartonStockIncome = document.createElement( 'a' );
	var btnCartonStockOutgo = document.createElement( 'a' );
	
	inpCartonSearch.type = 'number';
	inpCartonSearch.id = 'cartonSearch';
	divCartonInfo.id = 'cartonInfo';
	
	btnCartonDiscard.id = 'btnCartonDiscard';
	btnCartonEdit.id = 'btnCartonEdit';
	btnCartonPrint.id = 'btnCartonPrint';
	btnCartonStockIncome.id = 'btnCartonStockIncome';
	btnCartonStockOutgo.id = 'btnCartonStockOutgo';
	
	// add event listener
	divCartonSearch.addEventListener( 'keyup', function(e){
		if(  Main.getInstance().barcodescanner.state == 0
				&& ((window.event && e.keyCode == 13 ) || e.which == 13) ){
			Carton.search();
		}
	} );
	btnCartonAdd.addEventListener( 'click', Carton.add );
	btnCartonEdit.addEventListener( 'click', Carton.edit );
	btnCartonDiscard.addEventListener( 'click', Carton.discard );
	btnCartonPrint.addEventListener( 'click', Carton.print );
	btnCartonStockIncome.addEventListener( 'click', Carton.showStockIncome );
	btnCartonStockOutgo.addEventListener( 'click', Carton.showStockOutgo );
	
	// add classes
	divCartonSearch.className = 'group_left';
	btnCartonAdd.className = 'button green';
	btnCartonDiscard.className = 'button red';
	btnCartonEdit.className = 'button';
	btnCartonPrint.className = 'button';
	btnCartonStockIncome.className = 'button tinytext';
	btnCartonStockOutgo.className = 'button tinytext';
	divCartonInfo.className = "table_cell inline_text errortext";
	
	btnCartonEdit.style.display = 'none';
	btnCartonPrint.style.display = 'none';
	btnCartonDiscard.style.display = 'none';
	btnCartonStockIncome.style.display = 'none';
	btnCartonStockOutgo.style.display = 'none';
	
	// add content
	divCartonSearch.innerHTML = LANG( 'search' ) + " " + LANG( 'carton' ) + ": ";		
	divCartonInfo.innerHTML = LANG( 'carton_none_selected' );	
	btnCartonAdd.innerHTML = "<img src='img/action/add.png' />";
	btnCartonDiscard.innerHTML = "<img src='img/action/discard.png' />";
	btnCartonEdit.innerHTML = "<img src='img/action/edit.png' />";
	btnCartonPrint.innerHTML = "<img src='img/action/print.png' />";
	btnCartonStockIncome.innerHTML = "<img src='img/action/about.png' /><br />" + LANG('income');
	btnCartonStockOutgo.innerHTML = "<img src='img/action/about.png' /><br />" + LANG('outgo');
	
	// append elements
	divCartonSearch.appendChild( inpCartonSearch );	
	domCarton.appendChild( divCartonSearch );
	domCarton.appendChild( divCartonInfo );
	domCarton.appendChild( btnCartonAdd );
	domCarton.appendChild( btnCartonPrint );
	domCarton.appendChild( btnCartonStockIncome );
	domCarton.appendChild( btnCartonStockOutgo );
	domCarton.appendChild( btnCartonEdit );
	domCarton.appendChild( btnCartonDiscard );
	
	
	
	// ------------------ stock form ------------------
	var divRight = document.createElement( 'div' );
	var txtIncome = document.createElement( 'span' );
	var txtOutgo = document.createElement( 'span' );
	var btnMale = document.createElement( 'a' );
	var btnFemale = document.createElement( 'a' );
	var btnChildren = document.createElement( 'a' );
	var btnBaby = document.createElement( 'a' );
	var btnSummer = document.createElement( 'a' );
	var btnWinter = document.createElement( 'a' );
	var btnStockInfo = document.createElement( 'a' );
	var inpIncome = document.createElement( 'input' );
	var inpOutgo = document.createElement( 'input' );
	inpIncome.type = 'number';
	inpOutgo.type = 'number';
	inpIncome.style.width = "18%";
	inpOutgo.style.width = "18%";
	
	btnMale.id = 'btnMale';
	btnFemale.id = 'btnFemale';
	btnChildren.id = 'btnChildren';
	btnBaby.id = 'btnBaby';
	btnSummer.id = 'btnSummer';
	btnWinter.id = 'btnWinter';
	inpIncome.id = 'inpIncome';
	inpOutgo.id = 'inpOutgo';
	
	// add classes
	divRight.className = "table_cell inline_text";
	btnMale.className = 'button';
	btnFemale.className = 'button';
	btnChildren.className = 'button';
	btnBaby.className = 'button';
	btnSummer.className = 'button';
	btnWinter.className = 'button';
	btnStockInfo.className = "button";
	
	// add event listener
	btnMale.addEventListener( 'click', function(){
		var stock = Main.getInstance().warehouse.stock;
		stock.selectMale( !stock.maleSelected );
		Carton.updateAttributeButtons();
	} );
	btnFemale.addEventListener( 'click', function(){
		var stock = Main.getInstance().warehouse.stock;
		stock.selectFemale( !stock.femaleSelected );
		Carton.updateAttributeButtons();
	} );
	btnChildren.addEventListener( 'click', function(){
		var stock = Main.getInstance().warehouse.stock;
		stock.selectChildren( !stock.childrenSelected );
		Carton.updateAttributeButtons();
	} );
	btnBaby.addEventListener( 'click', function(){
		var stock = Main.getInstance().warehouse.stock;
		stock.selectBaby( !stock.babySelected );
		Carton.updateAttributeButtons();
	} );
	btnSummer.addEventListener( 'click', function(){
		var stock = Main.getInstance().warehouse.stock;
		stock.selectSummer( !stock.summerSelected );
		Carton.updateAttributeButtons();
	} );
	btnWinter.addEventListener( 'click', function(){
		var stock = Main.getInstance().warehouse.stock;
		stock.selectWinter( !stock.winterSelected );
		Carton.updateAttributeButtons();
	} );
	btnStockInfo.addEventListener( 'click', function(){
		 Category.showStock();
	} );
	inpIncome.addEventListener( 'focus', function(){
		var stock = Main.getInstance().warehouse.stock;
		stock.incomeSelected = true;
		stock.outgoSelected = false;
		inpIncome.className = 'highlight';
		inpOutgo.className = '';
		inpOutgo.value = '';
	} );
	inpIncome.addEventListener( 'keyup', function(e){
		if(  Main.getInstance().barcodescanner.state == 0
				&& ((window.event && e.keyCode == 13 ) || e.which == 13) ){
			Main.getInstance().warehouse.stock.addArticle( parseInt(inpIncome.value) );
		}
	} );
	inpOutgo.addEventListener( 'focus', function(){
		var stock = Main.getInstance().warehouse.stock;
		stock.incomeSelected = false;
		stock.outgoSelected = true;
		inpIncome.className = '';
		inpOutgo.className = 'highlight';
		inpIncome.value = '';
	} );
	inpOutgo.addEventListener( 'keyup', function(e){
		if( Main.getInstance().barcodescanner.state == 0
				&& ((window.event && e.keyCode == 13 ) || e.which == 13) ){
			Main.getInstance().warehouse.stock.addArticle( parseInt(inpOutgo.value)*(-1) );
		}
	} );
	
	// add content
	btnMale.innerHTML = "<img src='img/male_s.png' /> " + LANG('male');
	btnFemale.innerHTML = "<img src='img/female_s.png' /> " + LANG('female');
	btnChildren.innerHTML = "<img src='img/children_s.png' /> " + LANG('children');
	btnBaby.innerHTML = "<img src='img/baby_s.png' /> " + LANG('baby');
	btnSummer.innerHTML = "<img src='img/summer_s.png' /> " + LANG('summer');
	btnWinter.innerHTML = "<img src='img/winter_s.png' /> " + LANG('winter');
	btnStockInfo.innerHTML = "<img src='img/action/about.png' />";
	txtIncome.innerHTML = LANG('income') + ": ";
	txtOutgo.innerHTML = " " + LANG('outgo') + ": ";	
	
	// add elements
	domStockForm.appendChild( btnMale );
	domStockForm.appendChild( btnFemale );
	domStockForm.appendChild( btnChildren );
	domStockForm.appendChild( btnBaby );
	domStockForm.appendChild( btnSummer );
	domStockForm.appendChild( btnWinter );
	
	divRight.appendChild( txtIncome );
	divRight.appendChild( inpIncome );
	divRight.appendChild( txtOutgo );
	divRight.appendChild( inpOutgo );
	domStockForm.appendChild( divRight );
	domStockForm.appendChild( btnStockInfo );
	
}

/**
 * Updates acrticle attribute buttons
 */
Carton.updateAttributeButtons = function(){
	var btnMale = document.getElementById( 'btnMale' );
	var btnFemale = document.getElementById( 'btnFemale' );
	var btnChildren = document.getElementById( 'btnChildren' );
	var btnBaby = document.getElementById( 'btnBaby' );
	var btnWinter = document.getElementById( 'btnWinter' );
	var btnSummer = document.getElementById( 'btnSummer' );
	var stock = Main.getInstance().warehouse.stock;
	
	if( !stock.maleSelected ){	
		btnMale.className = 'button';
	} else {
		btnMale.className = 'button blue';
		btnChildren.className = 'button';
		btnBaby.className = 'button';
	}

	if( !stock.femaleSelected ){	
		btnFemale.className = 'button';
	} else {
		btnFemale.className = 'button purple';
		btnChildren.className = 'button';
		btnBaby.className = 'button';
	}
	
	if( !stock.childrenSelected ){
		btnChildren.className = "button";
	} else {
		btnChildren.className = 'button green';
		btnBaby.className = 'button';
		btnMale.className = 'button';
		btnFemale.className = 'button';
	}

	if( !stock.babySelected ){		
		btnBaby.className = 'button';
	} else {
		btnBaby.className = 'button orange';
		btnChildren.className = 'button';
		btnMale.className = 'button';
		btnFemale.className = 'button';
	}

	if( !stock.summerSelected ){
		btnSummer.className = 'button';
	} else {
		btnWinter.className = 'button';
		btnSummer.className = 'button yellow';
	}

	if( !stock.winterSelected ){
		btnWinter.className = 'button';
	} else {
		btnWinter.className = 'button turqouis';
		btnSummer.className = 'button';
	}
}
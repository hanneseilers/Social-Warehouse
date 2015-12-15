Barcodescanner = function(){
	
	/**
	 * Barcode states
	 * 
	 * 0:	Not scanning
	 * 1:	Scanning start sequence
	 * 2:	Scanning command sequence
	 * 3:	Command scanned
	 */
	this.state = 0;
	
	// bacrode cache
	this.cache = "";
	
	/**
	 * Registers barcode scanner
	 */
	this.register = function(){
		document.addEventListener( "keydown", this.checkForBarcode, true );
		document.addEventListener( "click", this.reset, true );
		console.debug( 'Registered barcode keylogger' );
	}
	
	/**
	 * Unregisters barcode scanner
	 */
	this.unregister = function(){
		document.removeEventListener( "keydown", this.checkForBarcode );
		document.removeEventListener( "click", this.reset );
		console.debug( 'Unregistered barcode keylogger' );
	}
	
	/**
	 * List of command functions
	 * Identifier is command sequence
	 */
	this.commands = {
			
			'SNP': function(command){
				// select no palette
				console.debug( "Select no palette scanned" );
				Palette.resetSelections();
			},
			
			'SNC': function(command){
				// select no carton
				console.debug( "Select no carton scanned" );
				Carton.load(-1);
			},
			
			'SNL': function(command){
				// select no location
				console.debug( "Select no location" );
				var locations = Location.getSelected();
				for( var i=0; i<locations.length; i++ ){
					locations[i].select( false );
				}
			},
			
			'MP': function(command){
				// move palette				
				console.debug( "Palette move scanned" );
				var selectedPalettes = Palette.getSelected();
				if( selectedPalettes.length > 0 ){
					selectedPalettes[0].move();
				}
			},
			
			'DP': function(command){
				// discard palette				
				console.debug( "Palette discard scanned" );
				var selectedPalettes = Palette.getSelected();
				if( selectedPalettes.length > 0 ){
					selectedPalettes[0].discard();
				}
			},
			
			'PP': function(command){
				// print palette
				console.debug( "Print palette scanned" );
				var palettes = Palette.getSelected();
				if( palettes.length > 0 ){
					palettes[0].print();
				}
			},
			
			'EC': function(command){
				// edit carton				
				console.debug( "Carton edit scanned" );
				var element = document.getElementById( 'btnCartonEdit' );
				if( element ){
					var event = new Event('click');
					element.dispatchEvent( event );
				}				
			},
			
			'DC': function(command){
				// discard carton				
				console.debug( "Carton discard scanned" );
				var element = document.getElementById( 'btnCartonDiscard' );
				if( element ){
					var event = new Event('click');
					element.dispatchEvent( event );
				}	
			},
			
			'PC': function(command){
				// print carton				
				console.debug( "Carton print scanned" );
				var element = document.getElementById( 'btnCartonPrint' );
				if( element ){
					var event = new Event('click');
					element.dispatchEvent( event );
				}	
			},
			
			'AC': function(command){
				// add new carton
				console.debug( "Cartonn add scanned" );
				Carton.add();
			},
			
			'SM': function(command){
				// select male			
				console.debug( "Select male scanned" );
				Main.getInstance().warehouse.stock.selectMale( true );
				Main.getInstance().warehouse.stock.selectFemale( false );
				Main.getInstance().warehouse.stock.selectBaby( false );
				Carton.updateAttributeButtons();
			},
			
			'SF': function(command){
				// select female			
				console.debug( "Select female scanned" );
				Main.getInstance().warehouse.stock.selectMale( false );
				Main.getInstance().warehouse.stock.selectFemale( true );
				Main.getInstance().warehouse.stock.selectBaby( false );
				Carton.updateAttributeButtons();
			},
			
			'SB': function(command){
				// select baby			
				console.debug( "Select baby scanned" );
				Main.getInstance().warehouse.stock.selectMale( false );
				Main.getInstance().warehouse.stock.selectFemale( false );
				Main.getInstance().warehouse.stock.selectBaby( true );
				Carton.updateAttributeButtons();
			},
			
			'SA': function(command){
				// select adults			
				console.debug( "Select adults scanned" );
				Main.getInstance().warehouse.stock.selectMale( true );
				Main.getInstance().warehouse.stock.selectFemale( true );
				Carton.updateAttributeButtons();
			},
			
			'SU': function(command){
				// select unisex/no-sex			
				console.debug( "Select unisex/no-sex scanned" );
				Main.getInstance().warehouse.stock.selectMale( false );
				Main.getInstance().warehouse.stock.selectFemale( false );
				Main.getInstance().warehouse.stock.selectBaby( false );
				Carton.updateAttributeButtons();
			},
			
			'SW': function(command){
				// select winter			
				console.debug( "Select winter scanned" );
				Main.getInstance().warehouse.stock.selectWinter( true );
				Carton.updateAttributeButtons();
			},
			
			'SS': function(command){
				// select summer			
				console.debug( "Select summer scanned" );
				Main.getInstance().warehouse.stock.selectSummer( true );
				Carton.updateAttributeButtons();
			},
			
			'SNS': function(command){
				// select no season			
				console.debug( "Select no season scanned" );
				Main.getInstance().warehouse.stock.selectSummer( false );
				Main.getInstance().warehouse.stock.selectWinter( false );
				Carton.updateAttributeButtons();
			},
			
			'SI': function(command){
				// select male			
				console.debug( "Select income scanned" );
				var element = document.getElementById( 'inpIncome' );
				if( element ){
					window.setTimeout( function(){
						element.focus();
					}, 1 );
				}
			},
			
			'SO': function(command){
				// select outgo			
				console.debug( "Select outgo scanned" );
				var element = document.getElementById( 'inpOutgo' );
				if( element ){
					window.setTimeout( function(){
						element.focus();
					}, 1 );
				}
			},
			
			'P': function(command){
				// select palette
				var paletteId = parseInt( command.substr( 1, command.length-1 ) );
				console.debug( "Palette " + paletteId + " scanned" );					
				
				var selectedPalettes = Palette.getSelected();
				if( selectedPalettes.length > 0 ){
					Palette.resetPalettesSelected();
				}
				
				var palette = Palette.getPalette( paletteId );
				if( palette ){
					Palette.showPalettesInSideList( [palette] );
					palette.select( true );
				}
			},
			
			'L': function(command){
				// select location
				var locationId = parseInt( command.substr( 1, command.length-1 ) );
				console.debug( "Location " + locationId + " scanned" );					
				var location = Location.getLocation(locationId);
				if( location )
					location.select( true );
			},
			
			'C': function(command){
				// select carton
				var cartonId = parseInt( command.substr( 1, command.length-1 ) );
				console.debug( "Carton " + cartonId + " scanned" );
				Carton.load( cartonId );
			}
	}
	
	/**
	 * Resets scanner state and cache
	 */
	this.reset = function(){
		Main.getInstance().barcodescanner.state = 0;
		Main.getInstance().barcodescanner.cache = "";
	}
	
	/**
	 * Scans keyboard event for barcode
	 */
	this.checkForBarcode = function(event){
		
		if( event ){
			// set barcode cache
			var key = "";
			if( event.keyCode > 31 || event.keyCode == 13 )
				key = String.fromCharCode( event.keyCode );
			key = key.toUpperCase();
			
			// get current state
			var state = Main.getInstance().barcodescanner.state;
			var cache = Main.getInstance().barcodescanner.cache;
			
			
			
			// check state machine
			if( state == 0 && key == 'S' ) state = 1;
			else if( state == 1 && key == 'W' ) state = 2;
			else if( state == 2 && cache.length > 4 && cache.endsWith( 'SW' ) ){
				
				var command = Main.getInstance().barcodescanner.cache;
				var command = command.substr( 2, command.length-4 );
				
				//execute command			
				try{
					var commands = Main.getInstance().barcodescanner.commands;
					for( var key in commands ){
						if( command.startsWith(key) ){
							commands[key]( command );
							console.debug( "FOUND " + key + ": " + command );
							break;
						}
					}
				} catch(err){
					console.error( "Barcode command not found\n" + err.message );
				}
				
				// prevent event default action
				event.preventDefault();
				state = 3;
				console.log("-------");
				if( key == String.fromCharCode(13) ){
					console.log("return detected");
				}
				
			}
			else if( state == 2 && key != String.fromCharCode(13) ){
				
				// prevent event default action
				event.preventDefault();
				
				// get target
				var target = null;
				if( event.path && event.path.length > 0 )
					target = event.path[0];
				else if( event.target )
					target = event.target;
				
				if( target && target.nodeName == 'INPUT' && target.value.endsWith('SW') ){
					var value = target.value;
					target.value	= value.substr( 0, value-2 );
				}
				
				state = 2;
				
			}
			else if( state == 3 || key.length > 0 ) state = 0;
			
			if( state > 0 ){
				Main.getInstance().barcodescanner.cache += key;
			} else {
				Main.getInstance().barcodescanner.reset();
			}
			
			// set new state
			Main.getInstance().barcodescanner.state = state;
			
		}
		
	}
	
}



/**
 * Gets Barcodescanner instance
 */
Barcodescanner.getInstance = function(){
	if( Barcodescanner.INSTANCE == null )
		Barcodescanner.INSTANCE = new Barcodescanner();
	
	return Barcodescanner.INSTANCE;
}
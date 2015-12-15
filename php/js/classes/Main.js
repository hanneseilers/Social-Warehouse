/**
 * Constructor
 * @param content	Element to show content in.
 */
function Main(content){
	
	// Variables
	arguments.callee.INSTANCE = this;	
	this.content = content;
	this.session = null;
	this.warehouse = null;
	this.barcodescanner = new Barcodescanner();
	
	// Functions
	
	/**
	 * Clears content area
	 */
	this.clearContent = function(){
		this.content.innerHTML = "";
	}
	
	/**
	 * Shows/Hides loading gif
	 * @param show	Set true to show loading gif, false otherwise.
	 */
	this.showLoading = function(show){
		if( !show )
			document.getElementById( 'loading' ).style.display = 'none';
		else
			document.getElementById( 'loading' ).style.display = 'block';
	}
	
	/**
	 * Shows warehouses list
	 */
	this.showWarehouses = function(){		
		var vSessionsStatus = document.createElement( 'div' );
		var vWarehouses = document.createElement( 'div' );
		var vRegister = document.createElement( 'div' );
		
		vSessionsStatus.className = 'tinytext righttext';
		vWarehouses.className = 'warehouseslist';
		
		this.clearContent();
		this.content.appendChild( vSessionsStatus );
		this.content.appendChild( vWarehouses );
		this.content.appendChild( vRegister );
		
		console.debug( 'Show warehouse list' );
		
		// show active sessiosn
		get( 'getActiveSessions', {}, function(data){			
			if( data ){
				vSessionsStatus.innerHTML = LANG('users_active') + ": " + data.response;
				console.debug( data.response + " active sessions" );
			}			
		} );
		
		// show warehouses
		Warehouse.listWarehouses( vWarehouses );
		
		// show register
		// TODO
	}
	
	/**
	 * Log in an shows data
	 * @param sessionId		Session ID or null, if to login into new session
	 * @param warehouseId	Warehouse ID (if not to recycle session)
	 * @param password		Password (if not to recycle session)
	 */
	this.login = function(sessionId, warehouseId, password){
		this.showLoading( true );
		
		if( sessionId != null ){
			// Recycle login
			console.debug( 'Try to recyle session #'+sessionId );
			
			Main.getInstance().session = new Session( sessionId );
			
			get( 'getSession', {}, function(data){
				if( data && data.response ){
					// logged in
					Main.getInstance().showWarehouse(data);
				} else {
					Session.reset();
					Main.getInstance().showWarehouses();
				}
			} );
			
			return true;
			
		} else if( warehouseId ) {
			// new login
			if( password && password.length > 0 ){
				
				// login
				password = MD5( password );
				
				this.clearContent();
				console.debug( 'Try to login' );
				
				get( 'login', {'warehouseId': warehouseId, 'pw': password}, function(data){
					if( data && data.response ){
						// logged in
						Main.getInstance().showWarehouse(data);			
					} else {
						// login failed
						showErrorMessage( LANG('login_failed') );
						Main.getInstance().showWarehouses();
					}
				} );
				
				return true;
				
			} else {
				showErrorMessage( LANG('login_invalid') );
			}
			
		} else  {		
			showErrorMessage( LANG('login_invalid') );
		}
		
		this.showLoading( false );
		return false;
	}
	
	/**
	 * Shows the warehouse if logged in.
	 * @param data	Session data repsone.
	 */
	this.showWarehouse = function(data){
		data = data.response;
		Main.getInstance().session = new Session( data.sessionId, data.warehouseId, data.restricted );
		Main.getInstance().warehouse = new Warehouse();
		Main.getInstance().warehouse.load( function(){
			Main.getInstance().warehouse.show( document.getElementById('menu') );
			Main.getInstance().warehouse.showContent( Main.getInstance().content );
			Main.getInstance().showLoading( false );
			Main.getInstance().barcodescanner.register();
		}, false);
	}
	
}

/**
 * Gets Main instance
 */
Main.getInstance = function(){
	if( Main.INSTANCE == null )
		Main.INSTANCE = new Main( document.getElementById('content') )
	
	return Main.INSTANCE;
}
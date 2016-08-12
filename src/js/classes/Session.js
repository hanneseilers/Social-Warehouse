/**
 * Session class
 * @param id	Session ID
 */
function Session(id, warehouseId, restricted){
	this.id = id;
	this.warehouseId = warehouseId;
	this.restricted = restricted;
	
	Session.reset();
	Cookies.set( 'sessionID', this.id );
}

/**
 * Function to reset session cookie
 */
Session.reset = function(){
	Cookies.remove( 'sessionID' );
}
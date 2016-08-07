var _sessionTimeoutTimer;
var _statusMessageTimer;

/**
 * Function to receive data from api
 * @param f			API function name
 * @param data		Data array
 * @param callback	Callback function
 */
get = function (f, data, callback){
	data = {'data': data};
	data.f = f;
	
	// check for session ID
	if( Main.getInstance().session != null )
		data.sessionId = Main.getInstance().session.id;
		
	// encode and send data
	json = base64_encode( JSON.stringify( data ) );
	$.get( "api/api.php", {'data': json}, function( data, status ){
		if( status == 'success' ){
			try{
				callback( JSON.parse(data) );
			} catch(err){
				console.error(err);
				console.error(data);
			}
		} else {
			callback( null );
		}
	} );
	resetCacheTimeout();
}

/**
 * New string prototype functions
 */
String.prototype.endsWith = function(suffix) {
    return this.indexOf(suffix, this.length - suffix.length) !== -1;
};

String.prototype.startsWith = function (str){
    return this.indexOf(str) === 0;
};

String.prototype.paddingLeft = function (paddingLength, paddingSequence) {
   var string = new String(this);
   var count = paddingLength - string.length;
   while( count > 0 ){
	   count -= 1;
	   string = paddingSequence + string;
   }
   return String(string);
};

String.prototype.paddingRight = function (paddingLength, paddingSequence) {
	   var string = new String(this);
	   var count = paddingLength - string.length;
	   while( count > 0 ){
		   count -= 1;
		   string += paddingSequence;
	   }
	   return String(string);
	};
	
function createTextElement(html){
	var element = document.createElement( 'span' );
	element.innerHTML = html;
	return element;
}

/**
 * Starts a timer that automatically calls a callback,
 * before session gets timed out.
 * @param callback	Callback function
 * @param before	Seconds before session times out,
 * 					at that the callback should be called.
 */
function startSessionTimer(callback, before){
	var timeout = parseInt(document.getElementById( 'session_maxtime' ).innerHTML) - (before != undefined ? before : 0);
	_sessionTimeoutTimer = window.setTimeout( callback, timeout*1000 );
}

/**
 * Resets the session timeout timer.
 * @param callback	Callback function
 * @param before	Seconds before session times out,
 * 					at that the callback should be called.
 */
function resetCacheTimeout(callback, before){
	window.clearTimeout( _sessionTimeoutTimer );
	startSessionTimer(callback, before);
}

/**
 * Shows a status message for 3 seconds.
 * @param message	Message to show
 * @param color		String of color of message background. Default is yellow.
 */
function showStatusMessage(message, color){
	var status = document.getElementById( 'status_message' );
	window.clearTimeout( _statusMessageTimer );
	status.innerHTML = message;
	status.style.display = 'block';
	
	if( typeof color != 'string' )
		color = 'yellow';
	
	if( !color.startsWith('#') )
		color = getStyleRuleValue( 'background-color', '.'+color );
		
	status.style.backgroundColor = color;
	
	_statusMessageTimer = window.setTimeout( hideStatusMessage, 3000 );
}

/**
 * Shows a error message for 3 seconds.
 * @param message	Message to show
 */
function showErrorMessage(message){
	showStatusMessage( message, "lightred" );
}

/**
 * Hides the status message
 */
function hideStatusMessage(){
	document.getElementById( 'status_message' ).style.display = 'none';
}

/**
 * Loads the main functions
 */
function load(){
	
	var vMain = Main.getInstance();
	
	// check for session cookie
	if( Cookies.get('sessionID') == null ){
		vMain.showWarehouses();						// no session > show warehouse list
	} else {
		vMain.login( Cookies.get('sessionID') );	// session found > try to login
	}
	
}
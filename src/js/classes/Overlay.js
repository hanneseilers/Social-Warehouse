/**
 * Overlay instance
 */
function Overlay(content, txtOK, txtCancel, callbackOK, callbackCancel){
	
	this.content = content;
	this.txtOK = txtOK;
	this.txtCancel = txtCancel;
	this.callbackOK = callbackOK;
	this.callbackCancel = callbackCancel;
	
	/**
	 * Function to show overlay
	 */
	this.show = function(){
		var overlay = document.getElementById( 'overlay' );
		var overlayContent = document.getElementById( 'overlay_content' );
		var overlayButtons = document.getElementById( 'overlay_buttons' );
		
		if( overlay && overlayContent && overlayButtons ){
			
			// set overlay height
			var body = document.body;
		    var html = document.documentElement;
			var height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
			overlay.style.height = height + 'px';
			
			
			// create buttons
			var btnOK = document.createElement( 'a' );
			var btnCancel = document.createElement( 'a' );
			
			// add button event listener
			var self = this;
			btnOK.addEventListener( 'click', function(){ self.ok(); } );
			btnCancel.addEventListener( 'click', function(){ self.cancel(); } )
			
			// add classes
			btnOK.className = "button block green";
			btnCancel.className = "button block lightred";
			
			// add button text
			btnOK.innerHTML = this.txtOK;
			btnCancel.innerHTML = this.txtCancel;
			
			// add to button content
			overlayButtons.innerHTML = "";
			
			if( this.txtOK )
				overlayButtons.appendChild( btnOK );			
			if( this.txtCancel )
				overlayButtons.appendChild( btnCancel );
				
			// set content
			overlayContent.innerHTML = "";
				
			if( typeof this.content == 'string' )
				overlayContent.innerHTML = this.content;
			else
				overlayContent.appendChild( this.content );
			
			// show overlay
			overlay.style.display = 'block';
			$(window).scrollTo( overlay, 500 );
			
		} else {
			console.error( 'Cannot find overlay containers.' );
		}
	}
	
	// Function to hide overlay
	this.hide = function(){
		var overlay = document.getElementById( 'overlay' );
		if( overlay )
			overlay.style.display = 'none';
	}
	
	// Callback if ok clicked
	this.ok = function(){
		if( this.callbackOK )
			this.callbackOK();
	}
	
	// Callback if cancel clicked
	this.cancel = function(){
		if( this.callbackCancel )
			this.callbackCancel();
	}
	
}

// Current overlay
Overlay.overlay = null;

/**
 * Generates new overlay
 * @param content			Content dom element
 * @param txtOK				Text for ok button
 * @param txtCancel			Text for cancel button
 * @param callbackOK		Function to call if ok button clicked
 * @param callbackCancel	Function to call if cancel button clicked
 */
Overlay.getOverlay = function(content, txtOK, txtCancel, callbackOK, callbackCancel){
	
	if( Overlay.overlay )
		Overlay.overlay.hide();
	
	Overlay.overlay = new Overlay( content, txtOK, txtCancel, callbackOK, callbackCancel );
	return Overlay.overlay;
	
}
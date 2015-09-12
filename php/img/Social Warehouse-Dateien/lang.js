LANGOBJ = {};

function LANG(key){
	
	if( LANGOBJ.hasOwnProperty('locale') && LANGOBJ['locale'].hasOwnProperty(key) )
		return LANGOBJ['locale'][key];
	else if( LANGOBJ.hasOwnProperty('default') && LANGOBJ['default'].hasOwnProperty(key) )
		return LANGOBJ['default'][key];
	
	return "err";
}

function loadLanguage(){
	if( !LANGOBJ.hasOwnProperty('default') || !LANGOBJ.hasOwnProperty('locale') ){
		
		// load language
		$.get( "lang/lang.php", {'lang': ''}, function(data, status){
			if( status == "success" )
				LANGOBJ = JSON.parse(data);
		} )
		
	}
}

loadLanguage();
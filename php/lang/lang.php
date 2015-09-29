<?php

	// get language
	$default_language = "en.php";
	if( !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ){
		$local_language = explode( ",", $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
		if( count($local_language) > 0 )
			$local_language = $local_language[0];
		$local_language = $local_language.".php";
	}
	
	// load deafult language
	include( __DIR__."/".$default_language );
	$GLOBALS['DEFLANG'] = $LANG;
	
	// load local language file
	if( file_exists(__DIR__."/".$local_language) )
		include( __DIR__."/".$local_language );
	$GLOBALS['LANG'] = $LANG;
	
	// check for get request
	if( isset($_GET['lang']) ){
		// generate json objects
		$json = [
				'default' => $DEFLANG,
				'locale' => $LANG,
		];
		print json_encode( $json );
	}
	
	function LANG($key){
		
		if( $GLOBALS['LANG'] && array_key_exists($key, $GLOBALS['LANG']) )
			return $GLOBALS['LANG'][$key];
		
		if( $GLOBALS['DEFLANG'] && array_key_exists($key, $GLOBALS['DEFLANG']) )
			return $GLOBALS['DEFLANG'][$key];
		
		return "err";
	}
	
	function HELP_FILE(){
		global $local_language, $default_language;
		if( file_exists( "help/".$local_language) )
			return "help/".$local_language;
		
		return "help/".$default_language;
	}

?>
<?php

// get language
$default_language = "en.php";
if( !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ){
	$local_language = explode( ",", $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
	if( count($local_language) > 0 )
		$local_language = $local_language[0];
	$local_language = "".$local_language.".php";
}

// load deafult language
include( $default_language );
$DEFLANG = $LANG;

// load local language file
if( file_exists($local_language) )
	include( $local_language );

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
	global $LANG, $DEFLANG;
	if( array_key_exists($key, $LANG) )
		return $LANG[$key];
	
	if( array_key_exists($key, $DEFLANG) )
		return $DEFLANG[$key];
	
	return "err";
}

function HELP_FILE(){
	if( file_exists( "help/".$local_language) )
		return "help/".$local_language;
	
	return "help/".$default_language;
}
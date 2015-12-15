<?php 

	/*
	 * Get list of countries from:
	 * http://countrylist.net/de/
	 */
	 
	include_once '../Log.php';

	function getCountries($file=null){
		if( $file == null )
			$file = realpath(dirname(__FILE__)).'/countries';
		
		Log::debug( 'Loading countries from '.getcwd().'/'.$file );
		$f = file( $file );
		$countries = array();
		foreach( $f as $country ){
			$data = explode(";", $country);
			if( is_numeric($data[0]) && count($data) > 4 )
				array_push( $countries, [$data[2], $data[4]] );
		}
		
		return $countries;
	}
	
	function getCountryDropdownOptions($file, $selected=""){
		$countries = getCountries($file);
		$html = "";
		foreach( $countries as $country ){
			$html = $html."\t<option";
			if( $country[0] == $selected )
				$html = $html." selected";
			$html = $html.">".$country[0]."</option>\n";
		}
		
		return $html;
	}
	
	function getCountryCode($file, $name){
		$countries = getCountries($file);
		foreach( $countries as $country ){
			if( $country[0] == $name )
				return strtolower( $country[1] );
		}
		
		return "";
	}

?>
<?php 

	/*
	 * Get list of countries from:
	 * http://countrylist.net/de/
	 */

	function getCountries($file){
		$f = file( $file );
		$countries = array();
		foreach( $f as $country ){
			$data = explode(";", $country);
			if( strcmp($data[0], "id") != 1 && count($data) > 4 )
				array_push( $countries, [$data[2], $data[4]] );
		}
		
		return $countries;
	}
	
	function getCountryDropdownOptions($file){
		$countries = getCountries($file);
		$html = "";
		foreach( $countries as $country ){
			$html = $html."\t<option>".$country[0]."</option>\n";
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
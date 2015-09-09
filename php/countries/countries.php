<?php 

	function getCountries($file){
		$f = file( $file );
		$countries = array();
		foreach( $f as $country ){
			array_push( $countries, $country );
		}
		
		return $countries;
	}
	
	function getCountryDropdownOptions($file){
		$countries = getCountries($file);
		$html = "";
		foreach( $countries as $country ){
			$html = $html."\t<option>".$country."</option>\n";
		}
		
		return $html;
	}

?>
<?php

/**
 * Abstract class for api request.
 * @author H. Eilers
 *
 */
abstract class Request extends stdClass implements JsonSerializable{
	protected $f = "";
	
	public function __construct($function){
		if( is_string($function) )
			$this->f = $function;
	}
	
	public static function isInstance($object){
		if( property_exists($object, 'f') )
			return true;
		return false;
	}
	
	/**
	 * Checks if object is instance of Request class.
	 * @param mixed $object	Object to check
	 * @return true if is Request class object, false otherwise.
	 */
	public function jsonSerialize(){
		$props = get_object_vars( $this );
		unset( $props['log'] );
		return $props;
	}
}

?>
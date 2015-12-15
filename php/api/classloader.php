<?php

	function __loadClass($class){
		include_once 'classes/'.$class.'.php';
	}
	
	spl_autoload_register( '__loadClass' );

?>
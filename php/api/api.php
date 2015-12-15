<?php
	/*
	 * API calls
	 * Set GET parameter function to call api function.
	 */
	include_once '../Log.php';
	include_once 'classloader.php';

	// check if data was send
	if( isset($_GET['data']) )
		print api( $_GET['data'] );		
	
	/**
	 * Function to process api request.
	 * @param string $data	Base64 encoded json string with api request data.
	 */
	function api($data){		
		// decode json data from get
		$data = base64_decode( $data );
		$data = json_decode( $data );
		
		// convert to login, logout or data request
		$response = null;
		$request = null;
		if( Login::isInstance($data) ){
			$request = new Login( $data->data->warehouseId, $data->data->pw );
			$response = $request->login();
				
		} elseif( Logout::isInstance($data) ){
			$request = new Logout( $data->sessionId );
			$response = $request->logout();
				
		} elseif( DataRequest::isInstance($data) ) {
			if( isset($data->sessionId) && isset($data->data) )
				$request = new DataRequest( $data->sessionId, $data->f, $data->data );
			elseif( isset($data->sessionId) )
				$request = new DataRequest( $data->sessionId, $data->f );
			else 
				$request = new DataRequest( 0, $data->f );
			$response = $request->process();
		}
		
		return json_encode( array('request' => $request, 'response' => $response) );
	}
	
?>
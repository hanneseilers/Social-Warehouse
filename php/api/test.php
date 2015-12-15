<?php

// include api
include_once '../Log.php';
include_once '../countries/countries.php';
include_once 'api.php';
include_once 'classloader.php';

Log::info( '<b>Starting API interface test</b>' );
$t1 = microtime(true);

// ---------- MAIN TEST AREA ----------

// get countries
$countries = getCountries();

// get warehouses
$request = new DataRequest( 0, 'getWarehouses' );
$response = test( $request )->response;
if( is_array($response) && count($response) == 0 ){

	// create warehouse
	$request = new DataRequest( 0, 'addWarehouse', [
			'name' => "Warehouse".rand( 0, 999 ),
			'description' => "Description",
			'country' => $countries[ rand(0, count($countries)) ][0],
			'city' => "Central City",
			'mail' => "mail@domain.com",
			'password' => md5("password")
	] );
	$response = test( $request );
	if( $response->response )
		Log::info( 'Created new warehouse' );
	else Log::error( 'Cannot create new warehouse' );
	
}

// list warehouses
$request = new DataRequest( 0, 'getWarehouses' );
$response = test( $request );
Log::info( 'Available warehouses:' );
Log::info( '-------------------------------' );
foreach( $response->response as $warehouse ){
	Log::info( "#".$warehouse->id.": ".$warehouse->country.", ".$warehouse->city.": ".$warehouse->name );
}
Log::info( '-------------------------------' );
$warehouseId = $response->response[ rand(0, count($response->response)-1) ]->id;

// login
Log::info( 'Try to login into warehouse ID #'.$warehouseId );
$request = new Login( $warehouseId, md5('password') );
$response = test( $request );
$session = $response->response;

if( $session ){
	
	Log::info( 'Created session ID #'.$session->sessionId );

	// check active sessions
	$request = new DataRequest( 0, 'getActiveSessions' );
	$response = test( $request );
	Log::info( 'Active sessions = '.$response->response );
	
	// check login
	$request = new DataRequest( $session->sessionId, 'checkLogin' );
	$response = test( $request );
	if( $response->response )
		Log::info( 'Session active' );
	else Log::error( 'Session inactive' );
	
	// edit warehouse
	$request = new DataRequest( $session->sessionId, 'editWarehouse', [
			'name' => "Warehouse".$session->sessionId,
			'description' => "New Description",
			'country' => $countries[ rand(0, count($countries)) ][0],
			'city' => "Starlight City",
			'mail' => "mail@city.com",
			'password' => md5('password'),
			'passwordRestricted' => md5('restricted'),
			'disableLocationLess' => 0,
			'disablePaletteLess' => 1
	] );
	$response = test( $request );
	if( $response->response )
		Log::info( 'Warehouse edited' );
	else Log::error( 'Warehouse edit failed' );
	
	// get warehouse details
	$request = new DataRequest( $session->sessionId, 'getWarehouse', ['update' => true] );
	$response = test( $request );
	Log::info( $response->response );
	
	// get categories
	$request = new DataRequest( $session->sessionId, 'getCategories', ['parent' => -1] );
	$response = test( $request )->response;
	if( is_array($response) && count($response) == 0 ){
		
		// add categories
		$request = new DataRequest( $session->sessionId, 'addCategory', ['name' => "RootCategory1"] );
		$cat1 = test( $request )->response;
		$request = new DataRequest( $session->sessionId, 'addCategory', ['name' => "RootCategory2"] );
		$cat2 = test( $request )->response;
		$request = new DataRequest( $session->sessionId, 'addCategory', ['name' => "RootCategory3"] );
		$cat3 = test( $request )->response;
		if( $cat1 && $cat2 && $cat3 )
			Log::info( 'New root categories #'.$cat1.' #'.$cat2.' #'.$cat3." added" );
		
		// add subcategories
		$request = new DataRequest( $session->sessionId, 'addCategory', ['name' => "SubCategory11", 'parent' => $cat1] );
		$subcat11 = test( $request )->response;
		$request = new DataRequest( $session->sessionId, 'addCategory', ['name' => "SubCategory12", 'parent' => $cat1] );
		$subcat12 = test( $request )->response;
		$request = new DataRequest( $session->sessionId, 'addCategory', ['name' => "SubCategory21", 'parent' => $cat2] );
		$subcat21 = test( $request )->response;
		
		$request = new DataRequest( $session->sessionId, 'addCategory', ['name' => "SubCategory121", 'parent' => $subcat12] );
		$subcat121 = test( $request )->response;
		$request = new DataRequest( $session->sessionId, 'addCategory', ['name' => "SubCategory122", 'parent' => $subcat12] );
		$subcat122 = test( $request )->response;
		
	}
	
	// list categories hierarchy
	$request = new DataRequest( $session->sessionId, 'getCategories', ['parent' => -1] );
	$response = test( $request )->response;
	Log::info( "Available categories:" );
	foreach( $response as $category ){
		Log::info( '#'.$category->id.": ".$category->name." parent=".$category->parent );
	}
	
	// get locations
	$request = new DataRequest( $session->sessionId, 'getLocations', [] );
	$response = test( $request )->response;	
	if( is_array($response) && count($response) == 0 ){
	
		// add location
		$request = new DataRequest( $session->sessionId, 'addLocation', ['name' => 'storage '.$session->sessionId] );
		$loc = test( $request )->response;
		if( $loc )
			Log::info( 'Added location #'.$loc );
		else Log::error( 'Failed to add location' );
		
	}
	
	// list locations
	$request = new DataRequest( $session->sessionId, 'getLocations', [] );
	$response = test( $request )->response;
	Log::info( "Available locations:" );
	$locationId = null;
	foreach( $response as $location ){
		Log::info( '#'.$location->id.": ".$location->name );
		$locationId = $location->id;
	}
	
	// get palettes
	$request = new DataRequest( $session->sessionId, 'getPalettes', [] );
	$response = test( $request )->response;
	if( is_array($response) && count($response) == 0 ){
		$request = new DataRequest( $session->sessionId, 'addPalette' );
		test( $request );
		test( $request );
		test( $request );
	}
	
	// list palettes
	$request = new DataRequest( $session->sessionId, 'getPalettes', [] );
	$response = test( $request )->response;
	Log::info( 'Available palettes:' );
	$paletteId = null;
	foreach( $response as $palette ){
		Log::info( '#'.$palette->id.' location='.$palette->locationId );
		$paletteId = $palette->id;
	}
	
	// move palette to location
	if( $locationId != null && $paletteId != null ){
		$request = new DataRequest( $session->sessionId, 'editPalette', ['id' => $paletteId, 'locationId' => $locationId] );
		$response = test( $request )->response;
		if( $response )
			Log::info(  'Moved pallette #'.$paletteId.' to location #'.$locationId );
		else Log::error( 'cannot move pallette #'.$paletteId.' to location #'.$locationId );
	}
	
	// logout
	$request = new Logout( $session->sessionId );
	$response = test( $request );
	if( $response->response )
		Log::info( 'Logged out' );
	else Log::error( 'Logout failed' );
	
} else {
	Log::error( 'Login failed' );
}

// ---------- MAIN TEST AREA END ----------

$t2 = microtime(true);
Log::info( 'Test finished.' );
Log::info( '<i>Runtime = '.($t2-$t1)." seconds</i>" );
	



/**
 * Function for testing request data.
 * @param mixed $request	Request data object
 */
function test($request){
	$json = json_encode( $request );
	$data = base64_encode( $json );

	return json_decode( api( $data ) );
}

?>
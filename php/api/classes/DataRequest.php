<?php

/**
 * Class for general api data request.
 * @author H. Eilers
 *
 */
class DataRequest extends Request{
	protected  $sessionId = 0;
	protected $data = null;
	
	/**
	 * Constructor
	 * @param number $session	Session ID
	 * @param string $function	Function name
	 * @param unknown $data		Data array
	 */
	public function __construct($session=0, $function='', $data=[]){
		parent::__construct( $function );
		$this->sessionId = $session;
		$this->data = $data;
	}
	
	public function process(){
		// check if session is active
		$session = new Session( $this->sessionId );
		if( $session->sessionId > 0 ){
			
			// process restricted functions
			switch( $this->f ){					
				case 'checkLogin':
					return Login::checkLogin($session->sessionId);
					
				case 'getWarehouse':
					if( isset($this->data->update) ){
						$warehouse = new Warehouse( $session->warehouseId, true );
					} else 
						$warehouse = new Warehouse( $session->warehouseId );
					
					$warehouse->dMail = $warehouse->getMail();
					$warehouse->dDisableLocationLess = $warehouse->isLocationLessDisabled();
					$warehouse->dDisablePaletteLess = $warehouse->isPaletteLessDisabled();
					return $warehouse;
					
				case 'editWarehouse':
					if( !$session->restricted ){
						$data = $this->data;
						$warehouse = new Warehouse( $session->warehouseId );
						
						// update warehouse data
						if( isset($data->name) ) $warehouse->name = $data->name;
						if( isset($data->description) ) $warehouse->description = $data->description;
						if( isset($data->country) ) $warehouse->country = $data->country;
						if( isset($data->city) ) $warehouse->city = $data->city;
						if( isset($data->password) ) $warehouse->setPassword( $data->password );
						if( isset($data->passwordRestricted) ) $warehouse->setPasswordRestricted( $data->passwordRestricted );
						if( isset($data->mail) ) $warehouse->setMail( $data->mail );
						if( isset($data->disableLocationLess) ) $warehouse->setDisableLocationLess( $data->disableLocationLess );
						if( isset($data->disablePaletteLess) ) $warehouse->setDisablePaletteLess( $data->disablePaletteLess );
						
						// update database entry
						return $warehouse->edit();
					}					
					return false;
					
				case 'deleteWarehouse':
					if( !$session->restricted ){
						$warehouse = new Warehouse( $session->warehouseId );
						
						if( $warehouse->id > 0 && $warehouse->delete() )
							return $session->destroy();
						
					}
					return false;
					
				case 'addCategory':
					if( !$session->restricted && isset($this->data->name) ){
						$category = new Category( null, $session->warehouseId );
						
						$category->name = $this->data->name;						
						if( isset($this->data->parent) ) $category->parent = $this->data->parent;						
						if( $category->edit() )
							return $category->id;
					}
					return false;
					
				case 'getCategory':
					if( isset($this->data->id) && isset($this->data->update) )
						return new Category( $this->data->id, $session->warehouseId, $this->data->update );
					elseif( isset($this->data->id) )
						return new Category( $this->data->id, $session->warehouseId );
					return false;
					
					
				case 'deleteCategory':
					if( !$session->restricted && isset($this->data->id) ){
						$category = new Category( $this->data->id, $session->warehouseId );
						$category->delete();
					}
					
				case 'editCtageory':
					if( !$session->restricted && isset($this->data->id) ){
						$data = $this->data;
						$category = new Category( $this->data->id, $session->warehouseId );
						
						if( isset($data->name) ) $category->name = $data->name;
						if( isset($data->demand) ) $category->demand = $data->demand;
						if( isset($data->countInCartons) ) $category->countInCartons = $data->countInCartons;
						return $category->edit();
					}
					return false;
					
				case 'getCategories':
					if( isset($this->data->parent) )
						return Category::getCategories( $session->warehouseId, $this->data->parent );
					else
						return Category::getCategories( $session->warehouseId );
					
				case 'addLocation':
					if( !$session->restricted && isset($this->data->name) ){
						$location = new Location( null, $session->warehouseId );
						$location->name = $this->data->name;
						if( $location->edit() )
							return $location->id;
					}
					return false;
					
				case 'getLocation':
					if( isset($this->data->id) && isset($this->data->update) )
						return new Location( $this->data->id, $session->warehouseId, $this->data->update );
					elseif( isset($this->data->id) )
						return new Location( $this->data->id, $session->warehouseId );
					return false;
					
				case 'deleteLocation':
					if( !$session->restricted && isset($this->data->id) ){
						$location = new Location( $id, $session->warehouseId );
						$location.delete();
					}
					
				case 'editLocation':
					if( !$session->restricted && isset($this->data->id) && isset($data->name) ){
						$location = new Location( $this->data->id, $session->warehouseId );
						$location->name = $this->data->name;
						return $location->edit();
					}
					return false;
					
				case 'getLocations':
					return Location::getLocations( $session->warehouseId );
					
				case 'addPalette':
					if( !$session->restricted ){
						$palette = new Palette( null, $session->warehouseId );
						if( isset($this->data->location) ) $palette->locationId = $this->data->location;
						if( $palette->edit() )
							return $palette->id;
					}
					
				case 'getPalette':
					if( isset($this->data->id) && isset($this->data->update) )
						return new Palette( $this->data->id, $session->warehouseId, $this->data->update );
					elseif( isset($this->data->id) )
						return new Palette( $this->data->id, $session->warehouseId );
					return false;
					
				case 'deletePalette':
					if( !$session->restricted && isset($this->data->id) ){
						$palette = new Palette( $id, $session->warehouseId );
						$palette.delete();
					}
					
				case 'editPalette':
					if( !$session->restricted && isset($this->data->id) && isset($this->data->locationId) ){
						$palette = new Palette( $this->data->id, $session->warehouseId );
						$palette->locationId = $this->data->locationId;
						return $palette->edit();
					}
					return false;
					
				case 'getPalettes':
					return Palette::getPalettes( $session->warehouseId );
			}
			
		} else {
			
			// process unrestricted function
			switch( $this->f ){
				case 'getActiveSessions':
					return Session::getActiveSessionsNumber();
						
				case 'getWarehouses':
					return Warehouse::getWarehouses();
					
				case 'addWarehouse':
					$data = $this->data;
					
					if( isset($data->name) && isset($data->description)
							&& isset($data->country) && isset($data->city)
							&& isset($data->password) && isset($data->mail) ){
						
						$warehouse = new Warehouse();
						Log::debug( 'new warehouse'.$warehouse->id );
						$warehouse->name = $data->name;
						$warehouse->description = $data->description;
						$warehouse->country = $data->country;
						$warehouse->city = $data->city;
						$warehouse->setPassword( $data->password );
						$warehouse->setMail( $data->mail );
						
						return $warehouse->edit();
					}
					
					return false;
			}
			
		}
		
		return false;		
	}
	
	/**
	 * Checks if object is instance of DataRequest class.
	 * @param mixed $object	Object to check
	 * @return true if is DataRequest class object, false otherwise.
	 */
	public static function isInstance($object){
		if( parent::isInstance($object)
				&& property_exists($object, 'sessionId')
				&& property_exists($object, 'data') )
			return true;
		return false;
	}
}

?>
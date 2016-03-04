<?php

/**
 * Class for general api data request.
 * @author H. Eilers
 *
 */
class DataRequest extends Request{
	protected $sessionId = 0;
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
			
			// update session
			$session->update();
			
			// process restricted functions
			switch( $this->f ){					
				case 'checkLogin':
					return Login::checkLogin($session->sessionId);					
					
				case 'getSession':
					return $session;
					
				case 'getWarehouse':
					if( isset($this->data->update) ){
						$warehouse = new Warehouse( $session->warehouseId, $this->data->update );
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
					break;
					
				case 'deleteWarehouse':
					if( !$session->restricted ){
						$warehouse = new Warehouse( $session->warehouseId );
						
						if( $warehouse->id > 0 && $warehouse->delete() )
							return $session->destroy();
						
					}
					break;
					
				case 'addCategory':
					if( !$session->restricted && isset($this->data->name) ){
						$category = new Category( null, $session->warehouseId );
						
						$category->name = $this->data->name;						
						if( isset($this->data->parent) ) $category->parent = $this->data->parent;						
						if( $category->edit() )
							return $category->id;
					}
					break;
					
				case 'getCategory':
					if( isset($this->data->id) && isset($this->data->update) )
						return new Category( $this->data->id, $session->warehouseId, $this->data->update );
					elseif( isset($this->data->id) )
						return new Category( $this->data->id, $session->warehouseId );
					break;
					
					
				case 'deleteCategory':
					if( !$session->restricted && isset($this->data->id) ){
						$category = new Category( $this->data->id, $session->warehouseId );
						return $category->delete();
					}
					break;
					
				case 'editCategory':
					if( !$session->restricted && isset($this->data->id) ){
						$data = $this->data;
						$category = new Category( $this->data->id, $session->warehouseId );
						
						if( isset($data->name) ) $category->name = $data->name;
						if( isset($data->demand) ) $category->demand = $data->demand;
						if( isset($data->parent) ) $category->parent = $data->parent;
						// TODO: edit category attributes (male, female, ...)
						return $category->edit();
					}
					break;
					
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
						return true;
					}
					break;
					
				case 'getLocation':
					if( isset($this->data->id) && isset($this->data->update) )
						return new Location( $this->data->id, $session->warehouseId, $this->data->update );
					elseif( isset($this->data->id) )
						return new Location( $this->data->id, $session->warehouseId );
					break;
					
				case 'deleteLocation':
					if( !$session->restricted && isset($this->data->id) ){
						$location = new Location( $this->data->id, $session->warehouseId );
						return $location->delete();
					}
					break;
					
				case 'editLocation':
					if( !$session->restricted && isset($this->data->id) && isset($this->data->name) ){
						$location = new Location( $this->data->id, $session->warehouseId );
						$location->name = $this->data->name;
						return $location->edit();
					}
					break;
					
				case 'getLocations':
					return Location::getLocations( $session->warehouseId );
					
				case 'addPalette':
					$palette = new Palette( null, $session->warehouseId );
					if( isset($this->data->locationId) ) $palette->locationId = $this->data->locationId;
					if( $palette->edit() )
						return $palette->id;
					break;
					
				case 'getPalette':
					if( isset($this->data->id) && isset($this->data->update) )
						return new Palette( $this->data->id, $session->warehouseId, $this->data->update );
					elseif( isset($this->data->id) )
						return new Palette( $this->data->id, $session->warehouseId );
					break;
					
				case 'deletePalette':
					if( isset($this->data->id) ){
						$palette = new Palette( $this->data->id, $session->warehouseId );
						return $palette->delete();
					}
					break;
					
				case 'editPalette':
					if( isset($this->data->id) ){
						$palette = new Palette( $this->data->id, $session->warehouseId );
						if( isset($this->data->locationId) ) $palette->locationId = $this->data->locationId;
						return $palette->edit();
					}
					break;
					
				case 'getPalettes':
					return Palette::getPalettes( $session->warehouseId );
					
					
				case 'getCarton':
					if( isset($this->data->id) && isset($this->data->update) )
						return new Carton( $this->data->id, $session->warehouseId, null, null, $this->data->update );
					elseif( isset($this->data->id) )
						return new Carton( $this->data->id, $session->warehouseId );
					break;
					
				case 'addCarton':
					$locationId = null;
					$paletteId = null;
					
					if( isset($this->data->location) )
						$locationId = $this->data->location;
					if( isset($this->data->palette) )
						$paletteId = $this->data->palette;
					$carton = new Carton( null, $session->warehouseId, $locationId, $paletteId );
					return $carton->id;
				
				case 'deleteCarton':
					if( isset($this->data->id) ){
						$carton = new Carton( $this->data->id, $session->warehouseId );
						return $carton->delete();
					}
					break;
					
				case 'editCarton':
					if( isset($this->data->id)  ){
						$carton = new Carton( $this->data->id, $session->warehouseId );
						if( isset($this->data->location) ) $carton->locationId = $this->data->location;
						else $carton->locationId = null;
						if( isset($this->data->palette) ) $carton->paletteId = $this->data->palette;
						else $carton->paletteId = null;
						return $carton->edit();
					}
					break;
					
				case 'addArticle':
					if( isset($this->data->carton)
					&& isset($this->data->category)
					&& isset($this->data->amount) ){
						return Stock::addArticle(
							$this->data->carton,
							$this->data->category,
							(isset($this->data->male) ? $this->data->male : false),
							(isset($this->data->female) ? $this->data->female : false),
							(isset($this->data->children) ? $this->data->children : false),
							(isset($this->data->baby) ? $this->data->baby : false),
							(isset($this->data->winter) ? $this->data->winter : false),
							(isset($this->data->summer) ? $this->data->summer : false),
							($this->data->amount >= 0 ? $this->data->amount : 0),
							($this->data->amount < 0 ? $this->data->amount : 0)
						);
					}
					break;
					
				case 'getStock':
					return Stock::getStock(
						$session->warehouseId,
						(isset($this->data->carton) ? $this->data->carton : null),
						(isset($this->data->category) ? $this->data->category : null),
						(isset($this->data->palette) ? $this->data->palette : null),
						(isset($this->data->location) ? $this->data->location : null),
						(isset($this->data->male) ? $this->data->male : false),
						(isset($this->data->female) ? $this->data->female : false),
						(isset($this->data->children) ? $this->data->children : false),
						(isset($this->data->baby) ? $this->data->male : false),
						(isset($this->data->summer) ? $this->data->male : false),
						(isset($this->data->winter) ? $this->data->male : false),
						(isset($this->data->details) ? $this->data->details : false)
					);
					
				case 'getBarcodeUri':
					if( isset($this->data->text) ){
						
						// create barcode object
						$bc = new Barcode39( $this->data->text );
						if( isset($this->data->textSize) ) $bc->barcode_text_size = $this->data->textSize;
						if( isset($this->data->barThin) ) $bc->barcode_bar_thin = $this->data->barThin;
						if( isset($this->data->barThick) ) $bc->barcode_bar_thick = $this->data->barThick;
						
						// generate barcode image
						$img = "barcode_".mt_rand(0, 100).".png";
						$bc->draw( $img );
						
						// get data uri
						$uri = Barcode39::getDataURI( $img );
						unlink( $img );
						
						return $uri;
						
					}
					break;
					
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
					break;
				
				case 'getCountries':
					return getCountries();
					break;
					
				case 'getCountryCode':
					$data = $this->data;
					if( isset($data->name) ){ 
						return getCountryCode(null, $data->name);
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
		if( parent::isInstance($object) )
			return true;
		return false;
	}
}

?>
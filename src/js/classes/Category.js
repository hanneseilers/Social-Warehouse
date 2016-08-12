function Category(id, name, parentId, demand, male, female, children, baby, summer, winter, weight){
	
	this.id = id;
	this.name = name;
	this.parentId = (parentId >= 0 ? parentId : null);
	this.demand = (demand ? demand : 0);
	this.male = (male ? true : false );
	this.female = (female ? true : false );
	this.children = (children ? true : false);
	this.baby = (baby ? true : false );
	this.summer = (summer ? true : false );
	this.winter = (winter ? true : false );
	this.weight = (weight ? weight : 0);
	
	// DOM Elements
	this.domElement = null;
	this.domChildren = null;
	
	/**
	 * Attaches the category dom element to a DOM element.
	 * @param parent	The parent DOM element.
	 * @param recursive Also add all child categories to this DOM element.
	 */
	this.attachToDOM = function(root, recursive){
		
		// create elements
		this.domElement = document.createElement( 'li' );
		this.domChildren = document.createElement( 'ul' );
		
		// add content
		this.domElement.id = this.id;
		this.domElement.innerHTML = this.name;
		
		// add elements
		this.domElement.appendChild( this.domChildren );
		root.appendChild( this.domElement );
		
		// update all child elements
		if( recursive ){
			var children = Category.getCategories( null, null, this.id );
			for( var i=0; i<children.length; i++ ){			
				children[i].attachToDOM( this.domChildren, true );				
			}
		}		
		
	}
	
	/**
	 * Updateds the information about this category.
	 * @param callback	Function to call after update.
	 */
	this.edit = function(callback){
		get( 'editCategory', {
			'id': this.id,
			'name': this.name,
			'parent': this.parentId,
			'demand': this.demand,
			'male': this.male,
			'female': this.female,
			'children': this.children,
			'baby': this.baby,
			'summer': this.summer,
			'winter': this.winter,
			'weight': this.weight
			}, callback );
	}
	
	/**
	 * Deletes category.
	 * @param callback	Function to call after update.
	 */
	this.discard = function(callback){
		var dom = document.createElement( 'div' )
		dom.style.fontSize = "large";
		dom.innerHTML = LANG( 'category_delete_question' ).replace( '%', this.name )
		var self = this;
		
		var overlay = new Overlay( dom, LANG('delete'), LANG('cancel'),
			function(){
				get( 'deleteCategory', {'id': self.id}, callback );
				overlay.hide();
			},
			function(){
				overlay.hide();
				Main.getInstance().warehouse.stock.reloadCategories();
			} );
		overlay.show();
	}
	
	/**
	 * Gets parent hierarchy
	 * @param level		Maximum level to include (default: 3)
	 * @returns			List of parent ctageories, where first item is next parent 
	 */
	this.getParents = function(level){
		
		if( !level ){
			level = 3;
		}
		
		// get parents
		var i = 0;
		var parents = [];
		var parentId = this.parentId;
		while( i < level && parentId > 0 && parentId != null ){
			
			var parent = Category.getCategories( parentId, null, null );
			if( parent.length > 0 ){
				
				parents.push( parent[0] );
				parentId = parent[0].parentId;
				i++;
				
			} else {
				break;
			}
			
		}
		
		return parents;
		
	}
	
	/**
	 * Generates string of parents hierarchy
	 * @param level		Maximum level to include (default: 3)
	 * @return			String of parent hierarchy
	 */
	this.getParentsString = function(level){		
		var parents = this.getParents(level);
		var string = this.getName();
		
		for( var i=0; i<parents.length; i++ ){
			string = parents[i].getShortName() + " > " + string;
		}
		
		return string;		
	}
	
	this.getName = function(){
		return this.name;
	}
	
	this.getShortName = function(length){
		if( !length )
			length = 10;
		return String( this.getName() ).shorten(length, '.')
	}
	
	this.showStock = function(){
		var dom = document.createElement( 'div' );
		dom.innerHTML = "<img src='img/loading.gif' /> " + LANG('loading');
		
		// show overlay
		var overlay = new Overlay( dom, LANG('close'), null, function(){ overlay.hide(); }, null );
		overlay.show();
		
		// load stock data
		get( 'getStock', {'category': this.id}, function(data){
			if( data && data.response ){
				Stock.showStock( data.response, dom );				
			} else {
				dom.innerHTML = LANG('stock_no_data');
				dom.className = 'errortext';
			}
		} );
	}
	
}



// List of categories
Category.categories = [];

/**
 * Gets categories by id and/or name and/or parent.
 * Searching by name expects to have parameter name in name of category (not case sensitive).
 * @param id		ID of category
 * @param name		Name of category
 * @param parentId 	ID of parent category. Use -1 to show root categories.
 */
Category.getCategories = function(id, name=null, parentId=null){
	
	var categories = [];
	
	if( id != null ){
		
		// search by id
		for( var i=0; i < Category.categories.length; i++ ){
			if( Category.categories[i].id == id ){
				return [ Category.categories[i] ];
			}
		}
		
	} else if( name != null && parentId != null ){
		
		// search by name and parent
		name = name.toLowerCase();
		for( var i=0; i < Category.categories.length; i++ ){
			if( Category.categories[i].parentId == parentId && Category.categories[i].name.toLowerCase().indexOf(name) > -1 ){
				categories.push( Category.categories[i] );
			}
		}
		
	} else if( parentId != null ){
		
		// search by parent only
		if( parentId == -1 )
			parentId = null;
		
		for( var i=0; i < Category.categories.length; i++ ){
			if( Category.categories[i].parentId == parentId ){
				categories.push( Category.categories[i] );
			}
		}
		
	} else if( name != null ) {
		
		// search by name only
		name = name.toLowerCase();
		for( var i=0; i < Category.categories.length; i++ ){
			if( Category.categories[i].name.toLowerCase().indexOf(name) > -1 ){
				categories.push( Category.categories[i] );
			}
		}
		
	}
	
	return categories;
	
}

/**
 * Adds a new category.
 * @þaram category	Category to add.
 * @param callback	Callback function to call after category was added.
 */
Category.add = function(category, callback){
	if( category )
		get( 'addCategory', {'name': category.name, 'parent': category.parentId}, callback );
}

Category.showStock = function(){
	if( Main.getInstance().warehouse.stock.category ){
		 var category = Category.getCategories( Main.getInstance().warehouse.stock.category );
		 if( category.length > 0 )
			 category[0].showStock();
	 }
}

/**
 * Loads categories
 * @param callback	Callbakc function to call after data was loaded.
 */
Category.load = function(callback){
	get( 'getCategories', {'parent': -1}, function(data){		
		if( data.response ){
			var categories = data.response;
			var warehouseId = Main.getInstance().warehouse.id;
			
			// add and init categories
			Category.categories = [];
			for( var i=0; i<categories.length; i++ ){
				if( categories[i].warehouseId == warehouseId ){
					var category = new Category(
							categories[i].id,
							categories[i].name,
							categories[i].parent,
							categories[i].demand,
							categories[i].male,
							categories[i].female,
							categories[i].children,
							categories[i].baby,
							categories[i].summer,
							categories[i].winter,
							categories[i].weight);
					Category.categories.push( category );
				}
			}			
		}
		
		if( callback )
			callback();
			
	} );
}
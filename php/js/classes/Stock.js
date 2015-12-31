/**
 * Stock class
 */
function Stock(warehouseId){
	this.warehouseId = warehouseId;
	this.category = null;
	this.maleSelected = false;
	this.femaleSelected = false;
	this.childrenSelected = false;
	this.babySelected = false;
	this.summerSelected = false;
	this.winterSelected = false;
	this.incomeSelected = false;
	this.outgoSelected = false;
	
	// dom elements
	this.domElement = null;
	this.domCarton = null;
	this.domStock = null;
	this.domCategoryEdit = null
	this.domCategories = null;
	this.domStockForm = null;
	
	this.inpCategoryDemand = null;
	this.inpCategorySearch = null;
	this.txtCategoryName = null;
	this.chkCategoryAttrMale = null;
	this.chkCategoryAttrFemale = null;
	this.chkCategoryAttrBaby = null;
	this.chkCategoryAttrSummer = null;
	this.chkCategoryAttrWinter = null;
	this.categoriesTree = null
	
	/**
	 * Adds a new article
	 * @param amount	Amount of articles. If negative is amount is added to outgo, to income othwerise.
	 */
	this.addArticle = function(amount){		
		
		// check barcodescanner state, do not continiue if barcode reading is active (state > 0)
		var barcodescanner = Main.getInstance().barcodescanner;
		if( barcodescanner && barcodescanner.state == 0 ){
			
			// check if carton and category selected and if amount is not zero.
			if( Carton.selected != null && this.category != null && amount != 0 && !isNaN(amount) ){
				
				// get data
				var carton = Carton.selected;
				var location = Location.getLocation( carton.locationId );
				var palette = Palette.getPalette( carton.paletteId );
				var category = Category.getCategories( this.category, null, null );
				if( category.length > 0 )
					category = category[0];
				
				// get category parents
				var txtCategoryParents = category.getParentsString();
				
				// create overlay content
				var dom = document.createElement( 'div' );
				var cartonInfo = document.createElement( 'div' );
				var articleInfo = document.createElement( 'div' );
				cartonInfo.innerHTML = "<b>" + LANG('carton')
					+ ": #"	+ carton.id
					+ ", " + LANG('location') + " " + (location ? location.name : undefined)
					+ ", " + LANG('palette') + " #" + (palette ? palette.number : undefined ) + "</b>";
				articleInfo.innerHTML = "<font size='20pt''>" + amount + " x </font>"
					+ ( this.maleSelected ? "<img src='img/male.png' />" : "" )
					+ " " + ( this.femaleSelected ? "<img src='img/female.png' />" : "" )
					+ " " + ( this.childrenSelected ? "<img src='img/children.png />" : "" )
					+ " " + ( this.babySelected ? "<img src='img/baby.png' />" : "" )
					+ " " + ( this.summerSelected ? "<img src='img/summer.png' />" : "" )
					+ " " + ( this.winterSelected ? "<img src='img/winter.png' />" : "" )
					+ "<br />" + txtCategoryParents;
				
				dom.appendChild( cartonInfo );
				dom.appendChild( articleInfo );
				
				// create callbacks
				var self = this;
				var okCallback = function(){
					
					// btnOK callback
					get( 'addArticle', {
						'carton': Carton.selected.id,
						'category': self.category,
						'male': self.maleSelected,
						'female': self.femaleSelected,
						'children': self.childrenSelected,
						'baby': self.babySelected,
						'summer': self.summerSelected,
						'winter': self.winterSelected,
						'amount': amount
						}, function(data){
							
							// api callback
							if( data && data.response ){						
								showStatusMessage( LANG('article_added') );
								Carton.load(0);
							} else {
								showErrorMessage( LANG('article_add_failed') );
							}
							
							document.getElementById( 'inpIncome' ).value = '';
							document.getElementById( 'inpOutgo' ).value = '';
					} );
					
				};
				
				
				// create and show overlay
				var overlay = new Overlay( dom, LANG('save'), LANG('cancel'),
						function(){ overlay.hide(); okCallback(); },
						function(){ overlay.hide(); } );
				overlay.show();
				
			} else {
				showErrorMessage( LANG('carton_none_selected') );
			}
			
		}
		
	}
	
	/**
	 * Creates a stock content
	 * @param content	DOM element
	 */
	this.getDOMElement = function(){
		if( this.domElement == null ){
			
			// create elements
			this.domElement = document.createElement( 'div' );
			this.domCarton = document.createElement( 'div' );
			this.domStockForm = document.createElement( 'div' );			
			this.domCategorySearch = document.createElement( 'div' );
			this.domCategoryEdit = document.createElement( 'div' );
			
			var categoryAttributeSettings = document.createElement( 'span' );
			var categoryDemand = document.createElement( 'span' );
			var categoryDemandInfo = document.createElement( 'span' );
			this.inpCategoryDemand = document.createElement( 'input' );
			
			var categorySearch = document.createElement( 'span' );
			this.inpCategorySearch = document.createElement( 'input' );
			
			this.inpCategoryDemand.type = 'number';
			this.inpCategorySearch.type = 'text';
			
			
			// add classes
			this.domCategorySearch.className = "table_content divreset";
			this.domCategoryEdit.className = "table_content divreset";
			this.domCarton.className = "groupitem";
			this.domStockForm.className = "groupitem";
			
			categorySearch.className = "group_left";
			categoryDemand.className = "table_cell righttext inline_text";
			categoryAttributeSettings = "group_left";
			
			
			// add event listener
			this.inpCategoryDemand.addEventListener( 'keyup', function(e){
				if( (window.event && e.keyCode == 13 ) || e.which == 13 ){
					Main.getInstance().warehouse.stock.updateDemand();
				}
			} );
			
			this.inpCategorySearch.addEventListener( 'keyup', function(){				
				var tree = Main.getInstance().warehouse.stock.categoriesTree.jstree(true);
				window.setTimeout( function(){
					tree.search( Main.getInstance().warehouse.stock.inpCategorySearch.value );
				}, 100 );				
			} );
			
			categoryDemand.innerHTML = LANG( 'demand' ) + ": ";
			categorySearch.innerHTML = LANG( 'search' ) + ": ";
			
			this.inpCategorySearch.id = 'categorySearch';
			categoryDemandInfo.id = 'categoryName';
			
			
			// append to document
			this.domElement.appendChild( this.domCarton );
			this.domElement.appendChild( this.domStockForm );
			this.domElement.appendChild( this.domCategorySearch );
			this.domElement.appendChild( this.domCategoryEdit );
			
			categorySearch.appendChild( this.inpCategorySearch );
			this.domCategorySearch.appendChild( categorySearch );
			
			categoryDemand.appendChild( this.inpCategoryDemand )
			categoryDemand.appendChild( categoryDemandInfo );
			if( !Main.getInstance().session.restricted )
				this.domCategoryEdit.appendChild( categoryDemand );
			
			
			// initi carton DOM elements
			Carton.initDom( this.domCarton, this.domStockForm );
			
			
			// show categories list
			this.updateCategoriesList();
			
		}
		
		return this.domElement;
	}
	
	/**
	 * Updates demand for selected category.
	 */
	this.updateDemand = function(){
		var categoryId = this.category;
		var category = Category.getCategories( categoryId );
		if( category.length > 0 ){
			
			category[0].demand = this.inpCategoryDemand.value;
			category[0].edit( function(){
				showStatusMessage( LANG('category_updated') );
				Main.getInstance().warehouse.stock.reloadCategories();
			} );
			
		} else {
			showErrorMessage( LANG('category_update_failed') );
		}
	}
	
	/**
	 * Hides categories tree list and shows loading gif.
	 */
	this.setCategoriesLoading = function(){
		this.domCategories.innerHTML = "<img src='img/loading.gif' /> " + LANG('loading');
	}
	
	/**
	 * Reloads categories list from server.
	 * Hides category list while getting data.
	 */
	this.reloadCategories = function(){		
		Category.load( function(){			
			Main.getInstance().warehouse.stock.updateCategoriesList();			
		} );
		
	}
	
	/**
	 * Updates categories list.
	 * Requires that categories are loaded.
	 */
	this.updateCategoriesList = function(){
		
		// clear all elements from container
		if( this.domCategories && this.domCategories.parentElement ){
			this.domCategories.parentElement.removeChild( this.domCategories );
		}
		
		// create elements
		this.domCategories = document.createElement( 'div' );
		var catList = document.createElement( 'ul' );
		var catTreeRoot = document.createElement( 'li' );
		var catTreeCategories = document.createElement( 'ul' );
		this.domCategories.id = "categoryTree";
		catTreeRoot.innerHTML = LANG( 'categories' ).toUpperCase();
		catTreeRoot.id = "-1";
		
		// add mutation observer
		var observer = new MutationObserver( function(mutations){
			// disconnect observer
			observer.disconnect();
			
			// conditional select plugin for jstree to define if be able to select jstree node or not
			(function ($, undefined) {
			  "use strict";
			  $.jstree.defaults.conditionalselect = function () { return true; };
			  $.jstree.plugins.conditionalselect = function (options, parent) {
			    this.activate_node = function (obj, e) {
			      if(this.settings.conditionalselect.call(this, this.get_node(obj))) {
			        parent.activate_node.call(this, obj, e);
			      }
			    };
			  };
			})(jQuery);
			
			// change list to jstree
			$.jstree.defaults.core.themes.icons = false;
			$.jstree.defaults.core.themes.variant = "large";
			$.jstree.defaults.search.show_only_matches = true;
			$.jstree.defaults.search.show_only_matches_children = true;
			$('#categoryTree').jstree({
					'conditionalselect' : function (node) {
					return Main.getInstance().barcodescanner.state == 0;
			  	},
				"core" : { // core options go here
					"multiple" : false, // no multiselection
					"check_callback" : true,
				},
				// activate the state plugin on this instance
				"plugins" : (Main.getInstance().session.restricted ?
								["state", "search", "unique", "conditionalselect"] :					// restricted access
								["state", "search", "contextmenu", "unique", "conditionalselect"]) 		// admin access
			});
			Main.getInstance().warehouse.stock.categoriesTree = $('#categoryTree');
			var categoriesTree = Main.getInstance().warehouse.stock.categoriesTree;
			var dom = Main.getInstance().warehouse.stock.domCategories;
			
			// add listener	
			// selection changed
			categoriesTree.on( 'changed.jstree', function(e, data){
				if( data.selected.length > 0 ){
					console.debug( "Selected category #" + data.selected[0] );					
					Main.getInstance().warehouse.stock.setSelectedCategory( data.selected[0] );
				}
			} );
			
			// category created
			categoriesTree.on( 'create_node.jstree', function(e, data){
				console.debug( "Created category " + data.node.text + " with parent #" + data.node.parent );
				
				Main.getInstance().warehouse.stock.setCategoriesLoading();
				var category = new Category( null, data.node.text, data.node.parent );				
				Category.add( category, function(){
					showStatusMessage( LANG('category_added') );
					Main.getInstance().warehouse.stock.reloadCategories();
				} );
			} );
			
			// category renamed
			categoriesTree.on( 'rename_node.jstree', function(e, data){
				console.debug( "Renamde category #" + data.node.id + " to " + data.text );
				
				var category = Category.getCategories( data.node.id );
				if( category.length > 0 ){
					category = category[0];
					category.name = data.text;
					Main.getInstance().warehouse.stock.setCategoriesLoading();
					category.edit( function(){
						showStatusMessage( LANG('category_updated') );
						Main.getInstance().warehouse.stock.reloadCategories();
					} );
				} else {
					showErrorMessage( LANG('category_update_failed') );
					Main.getInstance().warehouse.stock.reloadCategories();
				}
			} );
			
			// category deleted
			categoriesTree.on( 'delete_node.jstree', function(e, data){
				console.debug( "Deleted category #" + data.node.id );
				
				var category = Category.getCategories( data.node.id );
				if( category.length > 0 ){
					category = category[0];
					Main.getInstance().warehouse.stock.setCategoriesLoading();
					category.discard( function(data){
						showStatusMessage( LANG('category_deleted') );
						Main.getInstance().warehouse.stock.reloadCategories();
					} );
				} else {
					showErrorMessage( LANG('category_delete_failed') );
					Main.getInstance().warehouse.stock.reloadCategories();
				}
			} );
			
			// category moved
			categoriesTree.on( 'move_node.jstree', function(e, data){
				console.debug( "Moved category #" + data.node.id + " to parent #" + data.parent );
				
				var category = Category.getCategories( data.node.id );
				if( category.length > 0 ){
					category = category[0];
					category.parentId = data.parent;
					Main.getInstance().warehouse.stock.setCategoriesLoading();
					category.edit( function(data){
						showStatusMessage( LANG('category_updated') );
						Main.getInstance().warehouse.stock.reloadCategories();
					} );
				} else {
					showErrorMessage( LANG('category_update_failed') );
					Main.getInstance().warehouse.stock.reloadCategories();
				}
			} );
			
			// category copied
			categoriesTree.on( 'copy_node.jstree', function(e, data){
				Console.log( "Added (copy) category " + data.node.text + " with parent #" + data.parent );
				
				Main.getInstance().warehouse.stock.setCategoriesLoading();
				var category = new Category( null, data.node.text, data.parent );
				Category.add( category, function(){
					showStatusMessage( LANG('category_added') );
					Main.getInstance().warehouse.stock.reloadCategories();
				} );
			} );
			
		} );
		var config = { attributes: true, childList: true, characterData: true };
		observer.observe( this.domCategories, config );
		
		// add categories
		var rootCategories = Category.getCategories( null, null, -1 );
		for( var i=0; i<rootCategories.length; i++ ){
			rootCategories[i].attachToDOM( catTreeCategories, true );
		}
		
		// append to document
		catList.appendChild( catTreeRoot )
		catTreeRoot.appendChild( catTreeCategories );
		this.domElement.appendChild( this.domCategories );
		this.domCategories.appendChild( catList );
		
	}
	
	/**
	 * Sets a category as selected.
	 * @param id	ID of selected category.
	 */
	this.setSelectedCategory = function(id){
		this.category = parseInt(id);
		var category = Category.getCategories( this.category );
		var element = document.getElementById( 'categoryName' );
		
		// select category attributes
		if( category.length > 0 ){			
			category = category[0];			
		} else {
			category = null;
		}
		
		// show category information
		if( element ){
			if( category ){
				element.innerHTML = " " + LANG( 'pcs' ) + " " + category.name + " (#" + category.id + ")";
				element.className = '';
				this.inpCategoryDemand.value = category.demand;
				
			} else {
				element.innerHTML = " " + LANG('category_select');
				element.className = 'errortext';
				this.inpCategoryDemand.value = 0;
			}
		}
	}
	
	/**
	 * Set male attribute
	 * @param select	Set true to select attribute, false otherwise.
	 */
	this.selectMale = function(select){
		if( !select ){
			this.maleSelected = false;
		} else {
			this.maleSelected = true;
			this.childrenSelected = false;
			this.babySelected = false;
		}
	}
	
	/**
	 * Set female attribute
	 * @param select	Set true to select attribute, false otherwise.
	 */
	this.selectFemale = function(select){
		if( !select ){
			this.femaleSelected = false;		
		} else {
			this.femaleSelected = true;
			this.childrenSelected = false;
			this.babySelected = false;
		}
	}
	
	/**
	 * Set children attribute
	 * @param select	Set true to select attribute, false otherwise.
	 */
	this.selectChildren = function(select){
		if( !select ){
			this.childrenSelected = false;
		} else {
			this.childrenSelected = true;
			this.babySelected = false;
			this.maleSelected = false;
			this.femaleSelected = false;
		}
	}
	
	/**
	 * Set baby attribute
	 * @param select	Set true to select attribute, false otherwise.
	 */
	this.selectBaby = function(select){
		if( !select ){
			this.babySelected = false;		
		} else {
			this.babySelected = true;
			this.childrenSelected = false;
			this.maleSelected = false;
			this.femaleSelected = false;
		}
	}
	
	/**
	 * Set summer attribute
	 * @param select	Set true to select attribute, false otherwise.
	 */
	this.selectSummer = function(select){
		if( !select ){
			this.summerSelected = false;
		} else {
			this.summerSelected = true;
			this.winterSelected = false;
		}
	}
	
	/**
	 * Set winter attribute
	 * @param select	Set true to select attribute, false otherwise.
	 */
	this.selectWinter = function(select){
		if( !select ){
			this.winterSelected = false;
		} else {
			this.summerSelected = false;
			this.winterSelected = true;
		}
	}
	
}

/**
 * Shows stock data inside DOM element.
 * @param data	Array of stock data entries.
 * @param dom	DOM element, where to show stock data.
 */
Stock.showStock = function( data, dom ){
	dom.innerHTML = "";
	var highlight = false;
	
	for( var i=0; i<data.length; i++ ){
		
		var entry = data[i];
		var category = Category.getCategories( entry.category );
		
		if( category.length > 0 ){						
			category = category[0];
		
			// create elements
			var domEntry = document.createElement( 'div' );
			var domLeft = document.createElement( 'span' );
			var txtAmount = document.createElement( 'span' );
			var txtCategory = document.createElement( 'span' );
			var domRight = document.createElement( 'span' );
			var imgMale = document.createElement( 'img' );
			var imgFemale = document.createElement( 'img' );
			var imgChildren = document.createElement( 'img' );
			var imgBaby = document.createElement( 'img' );
			var imgWinter = document.createElement( 'img' );
			var imgSummer = document.createElement( 'img' );
			
			// set classes
			domEntry.className = 'table';
			if( highlight )
				domEntry.className = 'table highlight';
			domLeft.className = 'group_left';
			domRight.className = 'table_cell';
			txtAmount.className = 'table_cell';
			txtCategory.className = 'table_cell';
			
			// set content
			txtAmount.innerHTML = "<span class='monospace'>" + String(entry.amount).paddingLeft(5, '&nbsp;') + "x </span>";
			txtCategory.innerHTML = "<span class='monospace'>" + category.getParentsString() + "</span>";
			imgMale.src = 'img/none_s.png';
			imgFemale.src = 'img/none_s.png';
			imgChildren.src = 'img/none_s.png';
			imgBaby.src = 'img/none_s.png';
			imgWinter.src = 'img/none_s.png';
			imgSummer.src = 'img/none_s.png';
			
			// add to content
			domLeft.appendChild( txtAmount );
			domLeft.appendChild( txtCategory );
			domEntry.appendChild( domLeft );
			domEntry.appendChild( domRight );
			
			// add images
			if( entry.male ) imgMale.src = 'img/male_s.png';
			if( entry.female ) imgFemale.src = 'img/female_s.png';
			if( entry.children ) imgChildren.src = 'img/children_s.png';
			if( entry.baby ) imgBaby.src = 'img/baby_s.png';
			if( entry.winter ) imgWinter.src = 'img/winter_s.png';
			if( entry.summer ) imgSummer.src = 'img/summer_s.png';
			domRight.appendChild( imgMale );
			domRight.appendChild( imgFemale );
			domRight.appendChild( imgChildren );
			domRight.appendChild( imgBaby );
			domRight.appendChild( imgWinter );
			domRight.appendChild( imgSummer );
			
			dom.appendChild( domEntry );
			
			highlight = !highlight;
			
		}
		
	}
}
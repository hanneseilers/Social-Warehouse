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
	
	this.inpCategoryWeight = null;
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
		
		// check barcodescanner state, do not continue if barcode reading is active (state > 0)
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
				console.log( this.childrenSelected );
				cartonInfo.innerHTML = "<b>" + LANG('carton')
					+ ": #"	+ carton.id
					+ ", " + LANG('location') + " " + (location ? location.name : undefined)
					+ ", " + LANG('palette') + " #" + (palette ? palette.number : undefined ) + "</b>";
				articleInfo.innerHTML = "<font size='20pt''>" + amount + " x </font>"
					+ ( this.maleSelected ? "<img src='img/male.png' />" : "" )
					+ " " + ( this.femaleSelected ? "<img src='img/female.png' />" : "" )
					+ " " + ( this.childrenSelected ? "<img src='img/children.png' />" : "" )
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
			
			var categoryInfo = document.createElement( 'span' );
			var categoryDemandInfo = document.createElement( 'span' );
			var categoryWeightInfo = document.createElement( 'span' );
			var btnUpdateCategory = document.createElement( 'input' );
			
			this.inpCategoryDemand = document.createElement( 'input' );
			this.inpCategoryWeight = document.createElement( 'input' );
			
			var categorySearch = document.createElement( 'span' );
			this.inpCategorySearch = document.createElement( 'input' );
			
			this.inpCategoryDemand.type = 'number';
			this.inpCategoryWeight.type = 'number';
			this.inpCategorySearch.type = 'text';
			btnUpdateCategory.type = 'submit';
			
			
			// add classes
			this.domCategorySearch.className = "table_content divreset";
			this.domCategoryEdit.className = "table_content divreset";
			this.domCarton.className = "groupitem";
			this.domStockForm.className = "groupitem";
			
			this.inpCategoryDemand.className = "input_limit";
			this.inpCategoryWeight.className = "input_limit";
			
			categorySearch.className = "group_left";
			categoryInfo.className = "table_cell inline_text hidetext";
			
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
			
			btnUpdateCategory.addEventListener( 'click', function(){
				// extract category from its id
				var categoryId = $('#categoryTree').jstree('get_selected')[0];
				console.log(categoryId);
				if( categoryId > 0 ){
					var category = Category.getCategories( categoryId );					
					if( category.length > 0 ){
						
						// change category settings and edit it
						category = category[0];
						console.log("categories: " + category);
						category.demand =  parseInt(Main.getInstance().warehouse.stock.inpCategoryDemand.value);
						category.weight =  parseInt(Main.getInstance().warehouse.stock.inpCategoryWeight.value);
						category.edit( function(data){
							if( data && data.response ){
								Main.getInstance().warehouse.stock.reloadCategories();
								showStatusMessage( LANG('category_updated') );
							} else {
								showStatusMessage( LANG('category_update_failed'), 'red' );
							}
						} );
						
					}
				}
			} );
			
			// add content
			categorySearch.innerHTML = LANG( 'search' ) + ": ";
			categoryWeightInfo.innerHTML = " " + LANG( 'weight_per_article' ) + ": ";
			btnUpdateCategory.value = LANG( 'category_update' );
			
			
			// set ids
			this.inpCategorySearch.id = 'categorySearch';
			categoryDemandInfo.id = 'categoryName';
			
			
			// append to document
			this.domElement.appendChild( this.domCarton );
			this.domElement.appendChild( this.domStockForm );
			this.domElement.appendChild( this.domCategorySearch );
			this.domElement.appendChild( this.domCategoryEdit );
			
			categorySearch.appendChild( this.inpCategorySearch );
			this.domCategorySearch.appendChild( categorySearch );
			
			categoryInfo.appendChild( categoryDemandInfo );
			categoryInfo.appendChild( createTextElement( "<br/>" + LANG( 'demand' ) + ": " ) ); 
			categoryInfo.appendChild( this.inpCategoryDemand )
			categoryInfo.appendChild( createTextElement( " " + LANG( 'pcs' ) + String("").paddingLeft(5, '&nbsp;') ) );
			
			categoryInfo.appendChild( categoryWeightInfo );
			categoryInfo.appendChild( this.inpCategoryWeight );
			categoryInfo.appendChild( createTextElement( " g" + String("").paddingLeft(10, '&nbsp;') )  );
			categoryInfo.appendChild( btnUpdateCategory );
			
			
//			if( !Main.getInstance().session.restricted )
				this.domCategoryEdit.appendChild( categoryInfo );
			
			
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
				element.innerHTML = category.name + " - ID: #" + category.id;
				element.className = '';
				this.inpCategoryDemand.value = category.demand;
				this.inpCategoryWeight.value = category.weight;
				
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
Stock.showStock = function( data, dom, showOutgo=false, showImgSpaces=true ){
	dom.innerHTML = "";
	var highlight = false;
	var amountTotal = 0;
	var weightTotal = 0;
	var lastLocation = "";
	
	
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
			var txtWeight = document.createElement( 'span' );
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
			domRight.className = 'group_right';
			txtAmount.className = 'table_cell';
			txtCategory.className = 'table_cell';
			txtWeight.className = 'table_cell';
			
			// set content
			amount = 0;
			if( showOutgo && entry.income <= entry.outgo ){
				amount = -entry.outgo;
			} else if( entry.income-entry.outgo > 0 ) {
				amount = entry.income-entry.outgo;
			}
			
			weightSign = " g";
			weight = category.weight * amount;
			if( weight > 1000.0 ){
				weight = weight / 1000.0;
				weightSign = "kg";
			}
			if( weight > 1000.0 ){
				weight = weight / 1000.0;
				weightSign = " t";
			}
			
			// check if to set category name or palette number
			if( entry.hasOwnProperty('number') )
				txtCategory.innerHTML = "<span class='monospace'>#" + (entry.number ? entry.number : 'undefined') + "</span>";
			else
				txtCategory.innerHTML = "<span class='monospace'>" + category.getParentsString() + "</span>";
			
			txtAmount.innerHTML = "<span class='monospace'>" + String(amount).paddingLeft(5, '&nbsp;') + "x </span>";
			txtWeight.innerHTML = "<span class='monospace'>"
				+ String( "(" + Math.round(weight*100)/100 + weightSign + ")" ).paddingLeft(10, '&nbsp;')
				+ "</span>";
			imgMale.src = 'img/none_s.png';
			imgFemale.src = 'img/none_s.png';
			imgChildren.src = 'img/none_s.png';
			imgBaby.src = 'img/none_s.png';
			imgWinter.src = 'img/none_s.png';
			imgSummer.src = 'img/none_s.png';
			
			// add to content
			domLeft.appendChild( txtAmount );
			domLeft.appendChild( txtWeight );
			domLeft.appendChild( txtCategory );
			domEntry.appendChild( domLeft );
			domEntry.appendChild( domRight );
			
			// set images
			if( entry.male ) imgMale.src = 'img/male_s.png';
			if( entry.female ) imgFemale.src = 'img/female_s.png';
			if( entry.children ) imgChildren.src = 'img/children_s.png';
			if( entry.baby ) imgBaby.src = 'img/baby_s.png';
			if( entry.winter ) imgWinter.src = 'img/winter_s.png';
			if( entry.summer ) imgSummer.src = 'img/summer_s.png';
			
			// add images
			if( showImgSpaces ) 					domRight.appendChild( createTextElement("|") );			
			if( entry.male || showImgSpaces ) 		domRight.appendChild( imgMale );			
			if( entry.female || showImgSpaces ) 	domRight.appendChild( imgFemale );
			
			if( showImgSpaces ) 					domRight.appendChild( createTextElement("|") );
			if( entry.children || showImgSpaces ) 	domRight.appendChild( imgChildren );
			if( entry.baby || showImgSpaces )		domRight.appendChild( imgBaby );
			
			if( showImgSpaces ) 					domRight.appendChild( createTextElement("|") );
			if( entry.winter || showImgSpaces )		domRight.appendChild( imgWinter );
			if( entry.summer || showImgSpaces )		domRight.appendChild( imgSummer );
			
			
			if( (!showOutgo && amount > 0) || (showOutgo && amount < 0) ){
				// check if location is available and changed
				if( entry.hasOwnProperty('name') && lastLocation != entry.name ){
					var txtLocation = createTextElement( (entry.name ? entry.name : 'undefined') + ":" );
					txtLocation.className = "boldtext";
					dom.appendChild( txtLocation );
					lastLocation = entry.name;
				}
				
				// add data
				dom.appendChild( domEntry );
				highlight = !highlight;
				
				weightTotal += category.weight * amount;
				var vAmount = amountTotal;
				amountTotal += amount;
				//console.log( vAmount + " += " + amount + " = " + amountTotal );
			}
			
		}
		
	}
	
	// add total amount and weight
	// create elements
	var domEntry = document.createElement( 'div' );
	var domLeft = document.createElement( 'span' );
	var txtAmount = document.createElement( 'span' );
	var txtWeight = document.createElement( 'span' );
	
	// set classes
	domEntry.className = 'table';
	domLeft.className = 'group_left';
	txtAmount.className = 'table_cell';
	txtWeight.className = 'table_cell';
	
	// set content
	weightSign = " g";
	if( weightTotal > 1000.0 ){
		weightTotal = weightTotal / 1000.0;
		weightSign = "kg";
	}
	if( weightTotal > 1000.0 ){
		weightTotal = weightTotal / 1000.0;
		weightSign = " t";
	}
	
	txtAmount.innerHTML = "<span class='monospace boldtext'>TOTAL: " + amountTotal + " articles "
		+ "= " + Math.round(weightTotal*100)/100 + weightSign
		+ "</span>";
	
	// add elements to content
	domLeft.appendChild( txtAmount );
	domLeft.appendChild( txtWeight );
	domEntry.appendChild( domLeft );
	dom.appendChild( createHrElement() );
	dom.appendChild( domEntry );
}
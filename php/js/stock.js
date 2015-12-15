var _male = false;
var _female = false;
var _baby = false;
var _income_selected = false;
var _outgo_selected = false;

function addToStock(category){
	var income = document.getElementById( 'income' ).value;
	var outgo = document.getElementById( 'outgo' ).value;
	
	if( income.startsWith('SW') || outgo.startsWith('SW') ){
		document.getElementById( 'income' ).value = "";
		document.getElementById( 'income' ).value = "";
		return;
	}
	
	document.getElementById( 'income' ).value = "";
	document.getElementById( 'income' ).value = "";
	
	get( {	'function': 'addToStock',
			'category': category,
			'location': (_location ? _location : "NULL"),
			'palette': (_palette ? _palette : "NULL"),
			'in': (income.length > 0 ? income : 0),
			'out': (outgo.length > 0 ? outgo : 0),
			'male': _male,
			'female': _female,
			'baby': _baby
			},
			
			function (data, status){
				if( status == "success" && data == "ok" ){					
					_loadCategories( updateUnits, category );
				}
			});
	
}

function updateColors(){
	try{
		
		// get colors
		var defColor = getStyleRuleValue( 'background-color', '.button' );
		var orange = getStyleRuleValue( 'background-color', '.orange' );
		var blue = getStyleRuleValue( 'background-color', '.blue' );
		var yellow = getStyleRuleValue( 'background-color', '.yellow' );
		var purple = getStyleRuleValue( 'background-color', '.purple' );
		
		// reset button background
		document.getElementById( 'button_male' ).style.backgroundColor = defColor;
		document.getElementById( 'button_female' ).style.backgroundColor = defColor;
		document.getElementById( 'button_baby' ).style.backgroundColor = defColor;
		document.getElementById( 'income' ).parentElement.style.backgroundColor = defColor;
		document.getElementById( 'outgo' ).parentElement.style.backgroundColor = defColor;
		
		// set color of selected options
		if( _baby ){
			document.getElementById( 'button_baby' ).style.backgroundColor = orange;
		} else {
			if( _male ){
				document.getElementById( 'button_male' ).style.backgroundColor = blue;
			}
			if( _female ){
				document.getElementById( 'button_female' ).style.backgroundColor = purple;
			}
		}
		
		if( _income_selected ){
			document.getElementById( 'outgo' ).value = "";
			document.getElementById( 'income' ).focus();
			document.getElementById( 'income' ).parentElement.style.backgroundColor = yellow;
		} else if( _outgo_selected ){
			document.getElementById( 'income' ).value = "";
			document.getElementById( 'outgo' ).focus();
			document.getElementById( 'outgo' ).parentElement.style.backgroundColor = yellow;
		}
		
	} catch(err){
		document.getElementById( 'error_message' ).innerHTML = err.message;
	}
}

function hasChildCategory(id){
	for( var i=0; i < _categories.length; i++ ){
		if( _categories[i]['parent'] == id ){
			return true;
		}
	}
	
	return false;
}

function getUnit(category){
	if( category != null && category['carton'] != null && category['carton'] == 1 )
		return LANG('cartons_short');
	return LANG('pieces_short');
}

function getCategory(id){
	if( id != null ){
		for( var i=0; i < _categories.length; i++ ){
			if( _categories[i]['id'] == id ){
				return _categories[i];
			}
		}
	}
	
	return null;
}

function getSubCategories(id){
	var categories = [];
	
	if( id != null ){
		for( var i=0; i < _categories.length; i++ ){
			if( _categories[i]['parent'] == id ){
				categories.push( _categories[i] );
			}
		}
	}
	
	return categories;
}

function getCategoryHierrachy(id, links){
	var hierarchy = "";
	while( id != null ){
		var category = getCategory(id);
		if( hierarchy.length != 0 ){
			hierarchy = " > " + hierarchy;
		}
		hierarchy = 
			(links ? "<a href='javascript: showCategories(" + id + ");' class='button' >" : "")
			+ category['name']
			+ (links ? "</a>" : "")
			+ hierarchy;
		id = category['parent'];
	}
	
	return hierarchy;
}


function addCategory(parent){
	if( _warehouseId > 0 ){
		
		name = document.getElementById( 'addcategory' ).value;
		
		if( name.startsWith('%%') ){
			document.getElementById( 'addcategory' ).value = "";
			return;
		}
		
		if( name.length > 0 ){
			get( {'function': 'addCategory', 'name': base64_encode(name), 'parent': (parent ? parent : "NULL")}, function(data, status){
				if( status == "success" && data == "ok" ){
					_rootId = parent;
					_loadCategories( showCategories, parent );
				} else {
					console.error( LANG('category_name_error') )
				}
			} );
		} else {
			console.error( LANG('category_name_missing') );
		}
	}
}

function deleteCategory(id){
	get( 	{'function': 'deleteCategory', 'id': id},
			function(data, status){
				if( status == "success" && data == "ok" ){
					category = getCategory(id);
					_rootId = category['parent'];
				} else {
					alert( LANG('delete_category_failed') );
					_rootId = id;
				}
				_loadCategories( showCategories );
	});
}

function editCategory(id){
	var name = document.getElementById( 'categoryname' ).value.trim();
	
	if( name.startsWith('%%') ){
		document.getElementById( 'categoryname' ).value;
		return;
	}
	
	var demand = 0;	
	if( document.getElementById( 'demand' ) ){
		demand = document.getElementById( 'demand' ).value;
		if( typeof x === "string" || x instanceof String ){
			document.getElementById( 'demand' ).value = 0;
			demand = 0;
		}
	}
	
	if( demand.length == 0 ){
		demand = 0;
	}
	
	var carton = 0;
	if( document.getElementById( 'cartons' ) )
		carton = document.getElementById( 'cartons' ).checked;
	
	var showDemand = 0;
	if( document.getElementById( 'showDemand' ) )
		showDemand = document.getElementById( 'showDemand' ).checked;
	
	get( 	{'function': 'editCategory', 'id': id, 'name': base64_encode(name), 'demand': demand, 'carton': carton, 'showDemand': showDemand},
			function(data, status){
		_loadCategories( showCategories, id );
	});
}

function updateStockLocation(){
	
	// get dome lement
	var element = document.getElementById( 'scrollTarget' );
	
	if( _tap == 1 ){
		
		// get location data
		var vLocation = null;
		if( _location ){
			vLocation = getLocation( _location ); 
		}
		
		// get palette data
		var vPalette = null;
		if( _palette ){
			vPalette = getPalette( _palette ); 
		}
		
		// update stock location
		element.innerHTML = LANG('categories')
			+ (vLocation ? ": " + vLocation['name'] : "" )
			+ (vPalette ? " #" + vPalette['name'] : "" );
		
	}
	
}

function updateUnits(rootId){
	
	_tap = 1;
	
	// check root id
	if( typeof rootId == undefined ){
		rootId = _rootId;
	}
	
	// get category root
	var root = getCategory(rootId);
	
	// create root category
	if( root != null ){
		// get stock info including all sub categories
		var stockTotal = getRecursiveStockTotal( root['id'] );
		
		// create html
		var element = document.getElementById( 'stock_breadcrumps' );
		element.innerHTML = "<a href='javascript: showCategories();' class='button'>Stock</a> > "
			+ getCategoryHierrachy(root['id'], true)
			+ " (" + stockTotal + " " + getUnit(root) + ")";		
	}
	
}

function showCategories(rootId){
	document.getElementById( 'loading' ).style.display = 'block';
	document.getElementById( 'datacontent' ).style.display = 'none';
	
	_tap = 1;
	
	// check root id
	if( typeof rootId == undefined ){
		rootId = _rootId;
	}
	
	// calculate class
	var vClass = 4;
	if( _categories.length < 4 ){
		vClass = 3;
	}
	
	// get location data
	var vLocation = null;
	if( _location ){
		vLocation = getLocation( _location ); 
	}
	
	// get palette data
	var vPalette = null;
	if( _palette ){
		vPalette = getPalette( _palette ); 
	}
	
	// get category root
	var root = getCategory(rootId);
	
	// check if location and/or palette are missing
	var locationLess = ((_warehouse['disableLocationLess'] && _location != null) || _warehouse['disableLocationLess'] == 0);
	var paletteLess = ((_warehouse['disablePaletteLess'] && _palette != null) || _warehouse['disablePaletteLess'] == 0);
	
	// check if to add income and outgo options
	var addIncomeOutgo = false;
	var vCategoryRoot = rootId;
	if( root != null && !hasChildCategory(root['id']) ){
		addIncomeOutgo = true;
		vCategory = getCategory(rootId);
		if( root )
			vCategoryRoot = root['parent'];
	}
	
	// create html
	var html = "<h1 id='scrollTarget'>" + LANG('categories') + "</h1>";
	
	// create root category
	if( root != null ){
		// get stock info including all sub categories
		var stockTotal = getRecursiveStockTotal( root['id'] );
		
		// create html
		html += "<div id='stock_breadcrumps'>"
			+ "<a href='javascript: showCategories();' class='button'>Stock</a> > "
			+ getCategoryHierrachy(root['id'], true)
			+ " (" + stockTotal + " " + getUnit(root) + ")"
			+ "</div>\n";		
	}
	
	// add add-category form
	if( !_restricted ){
		// add spacer
		html += "<div class='hspacer'></div>";
		
		html += "<div class='groupitem'><span class='group_left'>"
			+ LANG('category_name') + ": <input type='text' id='addcategory' onkeypress='if(event.keyCode == 13) addCategory(" + rootId + ");' /></span>" 
			+ "<a href='javascript: addCategory(" + rootId + ");' class='button'>" + LANG('add_category') + "</a></div>";
	}
	
	// check if location or palette needed
	if( locationLess && paletteLess ){
	
		// show options to add storage
		if( addIncomeOutgo ){
			// show gender buttons
			html += "<div class='table'>"
				+ "<a href='javascript: _male = !_male; _baby = false; updateColors();' id='button_male' class='button button4 table_cell'><img src='img/male.png' /><br />" + LANG('male') + "</a>"
				+ "<a href='javascript: _female = !_female; _baby = false; updateColors();' id='button_female' class='button button4 table_cell'><img src='img/female.png' /><br />" + LANG('female') + "</a>"
				+ "<a href='javascript: _baby = !_baby; _male = false; _female = false; updateColors();' id='button_baby' class='button button4 table_cell'><img src='img/baby.png' /><br />" + LANG('children_baby') + "</a>"
				+ "</div>";
			
			// show in and out fields
			html += "<div class='table'>"
				+ "<span class='button button3 table_cell biginput'>" + LANG('income') + "<br />"
				+ "<input id='income'  type='text' onfocus='_income_selected = true; _outgo_selected = false; updateColors();' onkeypress='if(event.keyCode == 13) addToStock(" + root['id'] + ");' /></span>"
				+ "<span class='button button3 table_cell biginput'>" + LANG('outgo') + "<br />"
				+ "<input id='outgo'  type='text' onfocus='_income_selected = false; _outgo_selected = true; updateColors();' onkeypress='if(event.keyCode == 13) addToStock(" + root['id'] + ");' /></span>"
				+ "<a href='javascript: addToStock(" + root['id'] + ");' id='button_add' class='button button3 table_cell biginput green'>"
				+ (!vPalette ? LANG('add_to_loose_stock') : LANG('add_to_palette') + "<br />" + vPalette['name']) + "</a>";
				
			html += "</div>";
		}
	
	} else {
		html += "<div class='errortext'>"
		if( !locationLess && !paletteLess )
			html += LANG('location_selection_missing') + " " + LANG('palette_selection_missing');
		else if( !paletteLess )
			html += LANG('palette_selection_missing');
		else
			html += LANG('location_selection_missing');
		
		html += "</div>";
	}
	
	// create sub categories
	var row = 0;
	for( var i=0; i < _categories.length; i++ ){		
		if( _categories[i]['parent'] == vCategoryRoot ){
			// check if to open row
			if( row == 0 ){
				html += "\n<div class=' table'>";
			}
			
			// set link
			var href = "showCategories(" + _categories[i]['id'] + ");";
			
			// add button
			html += "\t<a href='javascript: " + href + "' class='button button"+ vClass
					+ " table_cell blue bigbutton'>"
					+ _categories[i]['name'] + "</a>\n";
			row++;
			
			// check if to cloes row
			if( row == vClass ){
				row = 0;
				html += "</div>\n"
			}
		}
	}
	
	// check if last row was closed
	if( row < vClass ){
		html += "</div>";
	}
			
	if( root != null && !_restricted ){	
		// add spacer
		html += "<div class='hspacer'></div>";
		
		// add category edit & delete form
		html += "<div class='groupitem'><span class='group_left'>"
			+ LANG('category_name') + ": <input type='text' id='categoryname' value='" + root['name'] + "' size=10 /> "
			+ LANG('demand') + ": <input type='number' id='demand' value='" + root['required'] + "' /> " + getUnit(root) + " "
			+ "<input type='checkbox' id='cartons' " + (root['carton'] == 1 ? "checked" : "" )  + " /> " + LANG('category_count_in_cartons') + " "
			+ "<input type='checkbox' id='showDemand' " + (root['showDemand'] == 1 ? "checked" : "" )  + " /> " + LANG('category_show_demand')
			+ "</span>";
		
		html += "<a href='javascript: editCategory(" + root['id'] + ");' class='button green'>" + LANG('edit') + "</a> "
			+ "<a href='javascript: deleteCategory(" + root['id'] + ");' class='button red'>"
			+ LANG('delete_category') + " '" + root['name'] + "'</a>"
			+ "</div>";
	}
	
	// show data
	showHtml(html);
	updateStockLocation();
	
	// update buttons and inputs
	if( addIncomeOutgo ){
		updateColors();	
	}
	
	// scroll to categories overview
	$.scrollTo( document.getElementById('scrollTarget') );
}

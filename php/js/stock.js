_male = false;
_female = false;
_baby = false;
_income_selected = false;
_outgo_selected = false;

function addToStock(category){
	income = document.getElementById( 'income' ).value;
	outgo = document.getElementById( 'outgo' ).value;
	
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
					document.getElementById( 'income' ).value = "";
					document.getElementById( 'income' ).value = "";
					_loadCategories( _showCategories, category );
				}
			});
	
}

function updateColors(){
	// get colors
	defColor = getStyleRuleValue( 'background-color', '.button' );
	orange = getStyleRuleValue( 'background-color', '.orange' );
	blue = getStyleRuleValue( 'background-color', '.blue' );
	yellow = getStyleRuleValue( 'background-color', '.yellow' );
	purple = getStyleRuleValue( 'background-color', '.purple' );
	
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
}

function showStock(warehouseId){
	setWarehouseId( warehouseId );
	_loadCategories( _showCategories, null );
}

function hasChildCategory(id){
	for( var i=0; i < _categories.length; i++ ){
		if( _categories[i]['parent'] == id ){
			return true;
		}
	}
	
	return false;
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
	categories = [];
	
	if( id != null ){
		for( i=0; i < _categories.length; i++ ){
			if( _categories[i]['parent'] == id ){
				categories.push( _categories[i] );
			}
		}
	}
	
	return categories;
}

function getCategoryHierrachy(id){
	hierarchy = "";
	while( id != null ){
		category = getCategory(id);
		if( hierarchy.length != 0 ){
			hierarchy = " > " + hierarchy;
		}
		hierarchy = category['name'] + hierarchy;
		id = category['parent'];
	}
	
	return hierarchy;
}


function addCategory(parent){
	if( _warehouseId > 0 ){
		
		name = document.getElementById( 'addcategory' ).value;
		if( name.length > 0 ){
			get( {'function': 'addCategory', 'name': base64_encode(name), 'parent': (parent ? parent : "NULL")}, function(data, status){
				if( status == "success" && data == "ok" ){
					_rootId = parent;
					_loadCategories( _showCategories, parent );
				} else {
					alert( LANG('category_name_error') )
				}
			} );
		} else {
			alert( LANG('category_name_missing') );
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
				_loadCategories( _showCategories );
	});
}

function editCategory(id){
	name = document.getElementById( 'categoryname' ).value.trim();
	
	demand = 0;
	if( document.getElementById( 'demand' ) )
		demand = document.getElementById( 'demand' ).value;
	
	if( demand.length == 0 ){
		demand = 0;
	}
	
	get( 	{'function': 'editCategory', 'id': id, 'name': base64_encode(name), 'demand': demand},
			function(data, status){
		_loadCategories( _showCategories, id );
	});
}

function _showCategories(rootId){
	if( typeof rootId == undefined ){
		rootId = _rootId;
	}
	
	// calculate class
	vClass = 3;
	if( _categories.length % 4 == 0 ){
		vClass = 4;
	} else if( _categories.length % 3 == 0 ){
		vClass = 3;
	} else if( _categories.length >= 4){
		vClass = 4;
	}
	
	// get location data
	vLocation = null;
	if( _location ){
		vLocation = getLocation( _location ); 
	}
	
	// get palette data
	vPalette = null;
	if( _palette ){
		vPalette = getPalette( _palette ); 
	}
	
	// get category root
	root = getCategory(rootId);
	
	// check if to add income and outgo options
	addIncomeOutgo = false;
	vCategoryRoot = rootId;
	if( root != null && !hasChildCategory(root['id']) ){
		addIncomeOutgo = true;
		vCategory = getCategory(rootId);
		if( root )
			vCategoryRoot = root['parent'];
	}
	
	// create html
	html = "<h1 " + (!addIncomeOutgo ? "id='scrollTarget'" : "")  + ">" + LANG('categories')
		+ (vLocation ? ": " + vLocation['name'] : "" )
		+ (vPalette ? " #" + vPalette['name'] : "" )
		+ "</h1>";
	
	// create root category
	if( root != null ){	
		// get stock info including all sub categories
		stock = getRecursiveStockInfo( root['id'] );
		
		// create html
		html += "<a href='javascript: _showCategories("
			+ root['parent'] + ");' class='button centertext block'>"
			+ getCategoryHierrachy(root['id'])
			+ " (" + stock['total'] + LANG('pieces_short') + ")"
			+ "</a>\n";
	}	
	
	// create sub categories
	row = 0;
	for( var i=0; i < _categories.length; i++ ){		
		if( _categories[i]['parent'] == vCategoryRoot ){
			// check if to open row
			if( row == 0 ){
				html += "\n<div class=' table'>";
			}
			
			// set link
			href = "_showCategories(" + _categories[i]['id'] + ");";
			
			// get stock info including all sub categories
			stock = getRecursiveStockInfo( _categories[i]['id'] );
			
			// add button
			html += "\t<a href='javascript: " + href + "' class='button button"+ vClass
					+ " table_cell blue bigbutton'>"
					+ _categories[i]['name'] + "<br /><span class='tinytext'>"
					+ stock['total'] + LANG('pieces_short')
					+ "</span></a>\n";
			row++;
			
			// check if to cloes row
			if( row == vClass-1 ){
				row = 0;
				html += "</div>\n"
			}
		}
	}
	
	// check if last row was closed
	if( row < vClass ){
		html += "</div>";
	}
			
	// add spacer
	html += "<div class='hspacer'></div>";
			
	// show options to add storage
	if( addIncomeOutgo ){		
		// show gender button
		html += "<div class='table' id='scrollTarget'>"
			+ "<a href='javascript: _male = !_male; _baby = false; updateColors();' id='button_male' class='button button4 table_cell'><img src='img/male.png' /><br />" + LANG('male') + "</a>"
			+ "<a href='javascript: _female = !_female; _baby = false; updateColors();' id='button_female' class='button button4 table_cell'><img src='img/female.png' /><br />" + LANG('female') + "</a>"
			+ "<a href='javascript: _baby = !_baby; _male = false; _female = false; updateColors();' id='button_baby' class='button button4 table_cell'><img src='img/baby.png' /><br />" + LANG('children_baby') + "</a>"
			+ "</div>";
		
		// show in and out fields
		html += "<div class='table'>"
			+ "<span class='button button3 table_cell biginput'>" + LANG('income') + "<br />"
			+ "<input id='income'  type='number' onfocus='_income_selected = true; _outgo_selected = false; updateColors();' onkeypress='if(event.keyCode == 13) addToStock(" + root['id'] + ");' /></span>"
			+ "<span class='button button3 table_cell biginput'>" + LANG('outgo') + "<br />"
			+ "<input id='outgo'  type='number' onfocus='_income_selected = false; _outgo_selected = true; updateColors();' onkeypress='if(event.keyCode == 13) addToStock(" + root['id'] + ");' /></span>"
			+ "<a href='javascript: addToStock(" + root['id'] + ");' id='button_add' class='button button3 table_cell biginput green'>"
			+ (!vPalette ? LANG('add_to_loose_stock') : LANG('add_to_palette') + "<br />" + vPalette['name']) + "</a>";
			
		html += "</div>";
	}
		
	// add add-category form
	html += "<div class='groupitem'><span class='group_left'>"
		+ LANG('category_name') + ": <input type='text' id='addcategory' onkeypress='if(event.keyCode == 13) addCategory(" + rootId + ");' /></span>" 
		+ "<a href='javascript: addCategory(" + rootId + ");' class='button'>" + LANG('add_category') + "</a></div>";
	
	if( root != null ){	
		// add category edit & delete form
		html += "<div class='groupitem'><span class='group_left'>"
			+ LANG('category_name') + ": <input type='text' id='categoryname' value='" + root['name'] + "' />"
			+ (!hasChildCategory(root['id']) ? " " + LANG('demand') + ": <input type='text' id='demand' value='" + root['required'] + "' />" : "" )
			+ "</span>";
		
		html += "<a href='javascript: editCategory(" + root['id'] + ");' class='button green'>" + LANG('edit') + "</a> "
			+ "<a href='javascript: deleteCategory(" + root['id'] + ");' class='button red'>"
			+ LANG('delete_category') + " '" + root['name'] + "'</a>"
			+ "</div>";
	}
	
	// show data
	showHtml(html);
	
	// update buttons and inputs
	if( addIncomeOutgo ){
		updateColors();	
	}
	
	// scroll to categories overview
	$.scrollTo( document.getElementById('scrollTarget') );
}
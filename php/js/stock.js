_male = false;
_female = false;
_baby = false;
_estimated = false;
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
			'baby': _baby,
			'estimated': _estimated
			},
			
			function (data, status){
				alert(status + "\n" + data);
			});
	
}

function updateColors(){
	// get colors
	defColor = getStyleRuleValue( 'background-color', '.button' );
	orange = getStyleRuleValue( 'background-color', '.orange' );
	blue = getStyleRuleValue( 'background-color', '.blue' );
	yellow = getStyleRuleValue( 'background-color', '.yellow' );
	purple = getStyleRuleValue( 'background-color', '.purple' );
	lightred = getStyleRuleValue( 'background-color', '.lightred' );
	
	// reset button background
	document.getElementById( 'button_male' ).style.backgroundColor = defColor;
	document.getElementById( 'button_female' ).style.backgroundColor = defColor;
	document.getElementById( 'button_baby' ).style.backgroundColor = defColor;
	document.getElementById( 'button_estimated' ).style.backgroundColor = defColor;
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
	
	if( _estimated ){
		document.getElementById( 'button_estimated' ).style.backgroundColor = lightred;
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
	_loadCategories( _showCategories );
}

function hasChildCategory(id){
	for( i=0; i < _categories.length; i++ ){
		if( _categories[i]['parent'] == id ){
			return true;
		}
	}
	
	return false;
}

function getCategory(id, list=null){
	if( list == null ){
		list = _categories;
	}
	
	if( id != null ){
		for( i=0; i < list.length; i++ ){
			if( list[i]['id'] == id ){
				return list[i];
			}
		}
	}
	
	return null;
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


function addCategory(parent=null){
	if( _warehouseId > 0 ){
		
		name = document.getElementById( 'addcategory' ).value;
		if( name.length > 0 ){
			get( {'function': 'addCategory', 'name': base64_encode(name), 'parent': parent}, function(data, status){
				if( status == "success" && data == "ok" ){
					_rootId = parent;
					_loadCategories();
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
				_loadCategories();
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

function _showCategories(rootId=_rootId){	
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
	
	// create html
	html = "<h1 id='scrollTarget'>" + LANG('categories') + (vLocation ? ": " + vLocation['name'] : "" ) + "</h1>";
	
	// create root category
	root = getCategory(rootId);
	if( root != null ){		
		html += "<a href='javascript: _showCategories("
			+ root['parent'] + ");' class='button centertext block'>"
			+ getCategoryHierrachy(root['id']) + "</a>\n";
	}
	
	// check if to add income and oualert( status + "\n" + data );tgo options
	addIncomeOutgo = false;
	if( root != null && !hasChildCategory(root['id']) ){
		addIncomeOutgo = true;
	}
	
	// create sub categories
	row =0
	for( i=0; i < _categories.length; i++ ){		
		if( _categories[i]['parent'] == rootId ){
			// check if to open row
			if( row == 0 ){
				html += "\n<div class=' table'>";
			}
			
			// set link
			href = "_showCategories(" + _categories[i]['id'] + ");";
			
			// add button
			html += "\t<a href='javascript: " + href + "' class='button button"+ vClass
					+ " table_cell blue bigbutton'>"
					+ _categories[i]['name'] + "</a>\n";
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
		html += "<div class='table'>"
			+ "<a href='javascript: _male = !_male; _baby = false; updateColors();' id='button_male' class='button button4 table_cell'><img src='img/male.png' /><br />" + LANG('male') + "</a>"
			+ "<a href='javascript: _female = !_female; _baby = false; updateColors();' id='button_female' class='button button4 table_cell'><img src='img/female.png' /><br />" + LANG('female') + "</a>"
			+ "<a href='javascript: _baby = !_baby; _male = false; _female = false; updateColors();' id='button_baby' class='button button4 table_cell'><img src='img/baby.png' /><br />" + LANG('children_baby') + "</a>"
			+ "<a href='javascript: _estimated = !_estimated; updateColors();' id='button_estimated' class='button button4 table_cell'><img src='img/estimate.png' /><br />" + LANG('estimated') + "</a>"
			+ "</div>";
		
		// show in and out fields
		html += "<div class='table'>"
			+ "<span class='button button3 table_cell biginput'>" + LANG('income') + "<br />"
			+ "<input id='income'  type='number' onfocus='_income_selected = true; _outgo_selected = false; updateColors();' onkeypress='if(event.keyCode == 13) addToStock();' /></span>"
			+ "<span class='button button3 table_cell biginput'>" + LANG('outgo') + "<br />"
			+ "<input id='outgo'  type='number' onfocus='_income_selected = false; _outgo_selected = true; updateColors();' onkeypress='if(event.keyCode == 13) addToStock();' /></span>"
			+ "<a href='javascript: addToStock(" + root['id'] + ");' id='button_add' class='button button3 table_cell biginput green'>"
			+ (!vPalette ? LANG('add_to_loose_stock') : LANG('add_to_palette') + "<br />" + vPalette['name']) + "</a>";
			
		html += "</div>";
	}
		
	// add add-category form
	html += "<div class='groupitem'><span class='group_left'>"
		+ LANG('category_name') + ": <input type='text' id='addcategory' /></span>" 
		+ "<a href='javascript: addCategory(" + rootId + ");' class='button'>" + LANG('add_category') + "</a></div>";
	
	if( root != null ){	
		// add category edit & delete button
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

_warehouseId = 0;
_rootId = null;
_categories = [];
_palette = null

_addCallback = null;
_isLooseStorage = false;

_male = false;
_female = false;
_baby = false;
_income_selected = false;
_outgo_selected = false;

function setWarehouseId(id){
	_warehouseId = id;
	document.getElementById( 'loading' ).style.display = 'block';
	document.getElementById( 'datacontent' ).style.display = 'none';
}

function showHtml(html){
	document.getElementById( 'loading' ).style.display = 'none';
	document.getElementById( 'datacontent' ).style.display = 'block';
	document.getElementById( 'datacontent' ).innerHTML = html;
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
		document.getElementById( 'income' ).focus();
		document.getElementById( 'income' ).parentElement.style.backgroundColor = yellow;
	} else if( _outgo_selected ){
		document.getElementById( 'outgo' ).focus();
		document.getElementById( 'outgo' ).parentElement.style.backgroundColor = yellow;
	}
}

function _loadCategories(){
	get( {'function': 'getCategoryInfos'}, function(data, status){
		if( status == "success" ){
			_categories = JSON.parse(data);
		}
		_showCategories();
	} )
}

function showCategories(warehouseId, addCallback=null, isLooseStorage=false){
	setWarehouseId( warehouseId );
	_isLooseStorage = isLooseStorage;
	_addCallback = addCallback;
	_loadCategories();
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
	get( {'function': 'editCategory', 'id': id, 'name': name, 'parent': parent} );
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
	
	// create html
	html = "<span id=\"scrollTarget\"></span>";
	
	// create root category
	root = getCategory(rootId);
	if( root != null ){		
		html += "<a href=\"javascript: _showCategories("
			+ root['parent'] + ", " + _addCallback + ");\" class=\"button centertext block\">" + getCategoryHierrachy(root['id']) + "</a>\n";
	}
	
	// check if to add income and outgo options
	addIncomeOutgo = false;
	if( _addCallback && _addCallback instanceof Function && root != null && !hasChildCategory(root['id']) ){
		if( _isLooseStorage || _palette ){
			addIncomeOutgo = true;
		}
	}
	
	// create sub categories
	row =0
	for( i=0; i < _categories.length; i++ ){		
		if( _categories[i]['parent'] == rootId ){
			// check if to open row
			if( row == 0 ){
				html += "\n<div class=\" table\">";
			}
			
			// set link
			href = "_showCategories(" + _categories[i]['id'] + ", " + _addCallback + ");";
			
			// add button
			html += "\t<a href=\"javascript: " + href + "\" class=\"button button"+ vClass
					+ " button_table_cell blue bigbutton\">"
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
	html += "<div class=\"hspacer\"></div>";
			
	// show options to add storage
	if( addIncomeOutgo ){		
		// show gender button
		html += "<div class=\"table\">"
			+ "<a href=\"javascript: _male = !_male; _baby = false; updateColors();\" id=\"button_male\" class=\"button button3 button_table_cell\"><img src=\"img/male.png\" /><br />" + LANG('male') + "</a>"
			+ "<a href=\"javascript: _female = !_female; _baby = false; updateColors();\" id=\"button_female\" class=\"button button3 button_table_cell\"><img src=\"img/female.png\" /><br />" + LANG('female') + "</a>"
			+ "<a href=\"javascript: _baby = !_baby; _male = false; _female = false; updateColors();\" id=\"button_baby\" class=\"button button3 button_table_cell\"><img src=\"img/baby.png\" /><br />" + LANG('children_baby') + "</a>"
			+ "</div>";
		
		// show in and out fields
		html += "<div class=\"table\">"
			+ "<span class=\"button button3 button_table_cell biginput\">" + LANG('income') + "<br />"
			+ "<input id=\"income\"  type=\"number\" onfocus=\"_income_selected = true; _outgo_selected = false; updateColors();\" onkeypress=\"if(event.keyCode == 13) " + _addCallback.name + "();\" /></span>"
			+ "<span class=\"button button3 button_table_cell biginput\">" + LANG('outgo') + "<br />"
			+ "<input id=\"outgo\"  type=\"number\" onfocus=\"_income_selected = false; _outgo_selected = true; updateColors();\" onkeypress=\"if(event.keyCode == 13) " + _addCallback.name + "();\" /></span>"
			+ "<a href=\"javascript: " + _addCallback.name + "();\" id=\"button_add\" class=\"button button3 button_table_cell biginput green\">"
			+ (_isLooseStorage ? LANG('add_loose_storage') : LANG('add_palette')) + "</a>";
			
		html += "</div>";
	}
		
	// add add-category form
	html += "<div class=\"groupitem\"><span class=\"group_left\">"
		+ LANG('category_name') + ": <input type=\"text\" id=\"addcategory\" /></span>" 
		+ "<a href=\"javascript: addCategory(" + rootId + ");\" class=\"button\">" + LANG('add_category') + "</a></div>";
	
	if( root != null ){	
		// add category edit & delete button
		html += "<div class=\"groupitem\"><span class=\"group_left\">"
			+ LANG('category_name') + ": <input type=\"text\" id=\"categoryname\" value=\"" + root['name'] + "\" />"
			+ (!hasChildCategory(root['id']) ? " " + LANG('demand') + ": <input type=\"text\" id=\"demand\" />" : "" )
			+ "</span>";
		
		html += "<a href=\"javascript: editCategory(" + root['id'] + ");\" class=\"button green\">" + LANG('edit') + "</a> "
			+ "<a href=\"javascript: deleteCategory(" + root['id'] + ");\" class=\"button red\">"
			+ LANG('delete_category') + " \"" + root['name'] + "\"</a>"
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
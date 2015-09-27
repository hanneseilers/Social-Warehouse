function showDemandStock(id){
	if( _categories.length == 0 ){
		_loadCategories( _showDemandStock2, id );
	} else {
		_showDemandStock2(id);
	}
}

function _showDemandStock2(id){	
	if( document.getElementById( 'stock_info_' + id ).style.display != "table-row" ){
		
		// get stock details
		if( document.getElementById( 'stock_loading_' + id ).style != "none" ){
			var tableElement = document.getElementById( 'stock_details_table_' + id );
			var lock = false;
			
			// add general stock data
			get( {'function': 'getCategoryStockInfo', 'category': id}, function(data, status){
				if( status == "success" ){
					var stock = JSON.parse(data);
					
					// wait until lock is release
					while( lock );
					lock = true;
					
					var rowIndex = getChildNodeIndex( document.getElementById( 'stock_details_overview_' + id ) ); 
					_showStockOverview( tableElement, rowIndex, stock );
					
					// release lock
					lock = false;
				}
			});
			
			// check if to load more details
			if( !hasChildCategory( id ) ){
				
				// add unlocated loose stock data
				get( {'function': 'getStockInfo', 'category': id, 'location': 'NULL', 'palette': 'NULL'}, function(data, status){
					if( status == "success" ){
						var stock = JSON.parse(data);
						
						// wait until lock is release
						while( lock );
						lock = true;
						
						var rowIndex = getChildNodeIndex( document.getElementById( 'stock_details_unlocated_loose_' + id ) )
						insertEmptyRow( tableElement, rowIndex );
						_showStockUnlocatedLoose( tableElement, rowIndex+1, stock );
						
						// release lock
						lock = false;
					}
				} );
				
				// add unlocated palettes
				get( {'function': 'getUnlocatedPalettesStockInfos', 'category': id, 'location': 'NULL'}, function(data, status){
					if( status == "success" ){
						var stock = JSON.parse(data);
						
						// wait until lock is release
						while( lock );
						lock = true;
						
						var rowIndex = getChildNodeIndex( document.getElementById( 'stock_details_unlocated_palette_' + id ) )
						insertEmptyRow( tableElement, rowIndex );
						_showStockUnlocatedPalettes( tableElement, rowIndex+1, stock );
						
						// release lock
						lock = false;
					}
				} );
				
			}
			
			// show table
			tableElement.style.display = "table";
			
		}
		
		// show stock details row
		document.getElementById( 'stock_info_' + id ).style.display = "table-row";
		
	} else {
		document.getElementById( 'stock_info_' + id ).style.display = "none";
	}
}

function _hideStockLoading(id){
	document.getElementById( 'stock_loading_' + id ).style.display = "none";
}

function insertStockInfo(tableElement, position, name, male, female, baby, unisex, asex){	
	// create new table row
	var row = tableElement.insertRow(position);
	var cellName = row.insertCell(-1);
	var cellMale = row.insertCell(-1);
	var cellFemale = row.insertCell(-1);
	var cellBaby = row.insertCell(-1);
	var cellUnisex = row.insertCell(-1);
	var cellAsex = row.insertCell(-1);
	
	// add content
	cellName.innerHTML = name;
	cellMale.innerHTML = "<img src='img/male_s.png' />" + male + LANG('pieces_short');
	cellFemale.innerHTML = "<img src='img/female_s.png' />" + female + LANG('pieces_short');
	cellBaby.innerHTML = "<img src='img/baby_s.png' />" + baby + LANG('pieces_short');
	cellUnisex.innerHTML = "<img src='img/unisex_s.png' />" + unisex + LANG('pieces_short');
	cellAsex.innerHTML = "<img src='img/asex_s.png' />" + asex + LANG('pieces_short');
}

function insertEmptyRow(tableElement, position){
	var row = tableElement.insertRow(position);
	var cell = row.insertCell(0);
	cell.innerHTML = "&#160;";
}

function getChildNodeIndex(childNode){
	var i = 0;
	while( (childNode = childNode.previousSibling) != null ) 
	  i++;
	
	return i;
}




function _showStockOverview(tableElement, rowIndex, stock){	
	insertStockInfo(tableElement, rowIndex, LANG('total'),
			stock['total']['male'], stock['total']['female'], stock['total']['baby'], stock['total']['unisex'], stock['total']['asex'] );
	insertStockInfo(tableElement, rowIndex+1, LANG('income'),
			stock['income']['male'], stock['income']['female'], stock['income']['baby'], stock['income']['unisex'], stock['income']['asex'] );
	insertStockInfo(tableElement, rowIndex+2, LANG('outgo'),
			stock['outgo']['male'], stock['outgo']['female'], stock['outgo']['baby'], stock['outgo']['unisex'], stock['outgo']['asex'] );
}

function _showStockUnlocatedLoose(tableElement, rowIndex, stock){
	var headerCell = tableElement.insertRow(rowIndex).insertCell(-1);
	headerCell.className = "text_bold";
	headerCell.colSpan = 6;
	headerCell.innerHTML = LANG('unlocated_stock') + ":";
	
	insertStockInfo(tableElement, rowIndex+1, LANG('loose_stock'),
			stock['male']['total'], stock['female']['total'], stock['baby']['total'], stock['unisex']['total'], stock['asex']['total'] );
}

function _showStockUnlocatedPalettes(tableElement, rowIndex, stock){
	for( var i=0; i<stock.length; i++ ){
	
		insertStockInfo(tableElement, rowIndex+i, "#"+stock[i]['name'],
				stock[i]['stock']['male']['total'], stock[i]['stock']['female']['total'],
				stock[i]['stock']['baby']['total'], stock[i]['stock']['unisex']['total'],
				stock[i]['stock']['asex']['total'] );
		
	}
}
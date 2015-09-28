function showDemandStock(id){
	if( _categories.length == 0 )
		_loadCategories( _showDemandStock2, id );
	else
		_showDemandStock2( id );
}

function _showDemandStock2(id){
	if( _locations.length == 0 )
		_loadLocations( _showDemandStock3, id );
	else
		_showDemandStock3( id );
}

function _showDemandStock3(id){	
	if( document.getElementById( 'stock_info_' + id ).style.display != "table-row" ){
		
		// get stock details
		if( document.getElementById( 'stock_loading_' + id ).style != "none" ){
			var tableElement = document.getElementById( 'stock_details_table_' + id );
			var lock = false;
			var vHasChild = hasChildCategory( id );
			
			// show stock details row
			var vLastDisplay = document.getElementById( 'stock_info_' + id ).style.display;
			document.getElementById( 'stock_info_' + id ).style.display = "table-row";
			
			// add general stock data
			if( vLastDisplay.length == 0 ){
				// start if display
				get( {'function': 'getCategoryStockInfo', 'category': id}, function(data, status){
					if( status == "success" ){
//						console.log( "\nGENERAL STOCK DATA" );
//						console.log( data );
						var stock = JSON.parse(data);
						
						// wait until lock is release
						while( lock );
						lock = true;
						
						var rowIndex = getChildNodeIndex( document.getElementById( 'stock_details_overview_' + id ) ); 
						_showStockOverview( tableElement, rowIndex, stock );
						
						// release lock
						lock = false;
					}
					
					if( vHasChild )
						_hideStockLoading( id );
				});
				
				// check if to load more details
				if( !vHasChild ){
					
					// add unlocated loose stock data
					get( {'function': 'getStockInfo', 'category': id, 'location': 'NULL', 'palette': 'NULL'}, function(data, status){
						if( status == "success" ){
//							console.log( "\nUNLOCATED LOOSE DATA" );
//							console.log( data );
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
//							console.log( "\nUNLOCATED PALETTES DATA" );
//							console.log( data );
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
						
						_hideStockLoading( id );
					} );
					
					// add locations
					for( var i=0; i<_locations.length; i++ ){
	
						get( {'function': 'getStockAtLocation', 'category': id, 'location': _locations[i]['id']}, function(data, status){
							if( status == "success" ){
//								console.log( "\nLOCATED STOCK DATA" );
//								console.log( data );
								var stock = JSON.parse(data);
									
								// check if to add located stock
								var numPalettes = Object.keys(stock['palettes']).length;
								if( stock['loose']['overall'] > 0 || numPalettes > 0 ){
									
									// wait until lock is release
									while( lock );
									lock = true;
								
									// add located stock header
									var location = getLocation( stock['request']['location'] );
									var rowIndex = getChildNodeIndex( document.getElementById( 'stock_details_located' + id ) );
									insertEmptyRow( tableElement, rowIndex );
									_addStockLocatedHeader( tableElement, rowIndex+1, location['name'] );
									rowIndex += 2;
									
									// show located loose stock
									if( stock['loose']['overall'] > 0 ){
										_showStockLocatedLoose( tableElement, rowIndex, stock['loose'], location['name'] );
										rowIndex++;
									}
									
									// show located palettes
									if( numPalettes > 0 ){
										insertEmptyRow( tableElement, rowIndex );
										_showStockLocatedPalettes( tableElement, rowIndex+1, stock['palettes'] );
									}
								
									// release lock
									lock = false;
								}
							}
						} );
					}
				} // end if display
				
			}
			
			// show table
			tableElement.style.display = "table";
		}
		
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
	while( childNode != null ){
		childNode = childNode.previousSibling;
		i++;
	}
	
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
	headerCell.innerHTML = "<hr />" + LANG('unlocated_stock') + ":";
	
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

function _addStockLocatedHeader( tableElement, rowIndex, locationName ){
	var headerCell = tableElement.insertRow(rowIndex).insertCell(-1);
	headerCell.className = "text_bold";
	headerCell.colSpan = 6;
	headerCell.innerHTML = "<hr />" + locationName + ":";
}

function _showStockLocatedPalettes(tableElement, rowIndex, stock){
	var i = 0;
	for( key in stock ){
		insertStockInfo(tableElement, rowIndex+i, "#"+stock[key]['name'],
				stock[key]['male'], stock[key]['female'],
				stock[key]['baby'], stock[key]['unisex'],
				stock[key]['asex'] );
		i++;
		
	}
}

function _showStockLocatedLoose(tableElement, rowIndex, stock){	
	insertStockInfo(tableElement, rowIndex, LANG('loose_stock'),
			stock['male']['total'], stock['female']['total'], stock['baby']['total'], stock['unisex']['total'], stock['asex']['total'] );
}
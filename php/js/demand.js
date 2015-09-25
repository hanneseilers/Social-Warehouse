function showDemandStock(id){	
	if( document.getElementById( 'stock_info_' + id ).style.display != "table-row" ){
		
		// get stock details
		if( document.getElementById( 'stock_loading_' + id ).style != "none" ){
			
			get( {'function': 'getCategoryStockInfo', 'category': id}, function(data, status){
				if( status == "success" ){
					var stock = JSON.parse(data);
					var tableElement = document.getElementById( 'stock_details_table_' + id )
					deleteAllRows( tableElement );
					
					// add general stock data
					_showStockOverview( tableElement, stock );
					
					// add unlocated stock data
					get( {'function': 'getStockInfo', 'category': id, 'location': 'NULL', 'palette': 'NULL'}, function(data, status){
						if( status == "success" ){
							var stock = JSON.parse(data);
							addEmptyRow( tableElement );
							_showStockUnlocated( tableElement, stock );
							
						} else _hideStockLoading(id);
					} );
				} else _hideStockLoading(id);
			});
			
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

function insertStockInfo(tableElement, name, male, female, baby, unisex, asex){	
	// create new table row
	var row = tableElement.insertRow(-1);
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

function deleteAllRows(tableElement){
	while(tableElement.hasChildNodes())
	{
		tableElement.removeChild(tableElement.firstChild);
	}
}

function addEmptyRow(tableElement){
	var headerCell = tableElement.insertRow(-1).insertCell(-1);
	headerCell.colSpan = 6;
	headerCell.innerHTML = "&#160";
}

function _showStockOverview(tableElement, stock){
	// add stock overview data
	insertStockInfo(tableElement, LANG('total'),
			stock['total']['male'], stock['total']['female'], stock['total']['baby'], stock['total']['unisex'], stock['total']['asex'] );
	insertStockInfo(tableElement, LANG('income'),
			stock['income']['male'], stock['income']['female'], stock['income']['baby'], stock['income']['unisex'], stock['income']['asex'] );
	insertStockInfo(tableElement, LANG('outgo'),
			stock['outgo']['male'], stock['outgo']['female'], stock['outgo']['baby'], stock['outgo']['unisex'], stock['outgo']['asex'] );
	
	// show table
	tableElement.style.display = "table";
}

function _showStockUnlocated(tableElement, stock){
	// add stock overview data	
	var headerCell = tableElement.insertRow(-1).insertCell(-1);
	headerCell.className = "text_bold";
	headerCell.colSpan = 6;
	headerCell.innerHTML = LANG('unlocated_stock') + ":";
	
	insertStockInfo(tableElement, LANG('total'),
			stock['male']['total'], stock['female']['total'], stock['baby']['total'], stock['unisex']['total'], stock['asex']['total'] );
	
	// show table
	tableElement.style.display = "table";
}
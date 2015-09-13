
function getRecursiveStockInfo(categoryId){	
	income = 0;
	outgo = 0;
	visited = [];
	not_visited = [categoryId];
	
	while( (id = not_visited.pop()) != undefined ){
		category = getCategory( id );
		if( category ){			
			// add category income and outgo
			visited.push( id );
			if( category['stockinfo']['income_total'] )
				income += Number(category['stockinfo']['income_total']);
			if( category['stockinfo']['outgo_total'] )
				outgo += Number(category['stockinfo']['outgo_total']);
			
			// search for sub categories
			subcategories = getSubCategories( id );			
			for( var i=0; i < subcategories.length; i++ ){
				if( visited.indexOf(subcategories[i]['id']) < 0 ){
					not_visited.push( subcategories[i]['id'] );
				}
			}			
		}
	}
	
	return { 'income_total': income, 'outgo_total': outgo, 'total': income-outgo };
}
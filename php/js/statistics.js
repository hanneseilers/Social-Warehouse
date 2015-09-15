
function getRecursiveStockTotal(categoryId){	
	var total = 0;
	var visited = [];
	var not_visited = [categoryId];
	var id;
	
	while( (id = not_visited.pop()) != undefined ){
		category = getCategory( id );
		visited.push( id );
		
		if( category ){			
			// add category income and outgo
			total += Number(category['stockinfo']['overall']);
			
			subcategories = getSubCategories( id );			
			for( var i=0; i < subcategories.length; i++ ){
				if( visited.indexOf(subcategories[i]['id']) < 0 ){
					not_visited.push( subcategories[i]['id'] );
				}
			}			
		}
	}
	
	return total;
}
function showDemandStock(id){
	if( document.getElementById( 'stock_info_' + id ).style.display != "table-row" ){
		document.getElementById( 'stock_info_' + id ).style.display = "table-row";
	} else {
		document.getElementById( 'stock_info_' + id ).style.display = "none";
	}
}
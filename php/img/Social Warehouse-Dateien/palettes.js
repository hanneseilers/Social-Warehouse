function _showPalettes(){
	html = "";
	
	// show data
	showHtml(html);
}

function showPalettes(warehouseId){
	setWarehouseId( warehouseId );
	loadLanguage( _showPalettes );
}
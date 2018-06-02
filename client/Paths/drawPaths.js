/*
*Функция отображающая метки A и B - пункт отправления и пункт прибытия соответственно. 
*/
var loc1, loc2;
var polylines = {};
function drawPaths(){
    	deletePaths();
	if (document.getElementById('routes-info')!=null) clickClose();
	map.on('click', onMapClick);
	function onMapClick(e) {
		if (loc1 == null) {
         		loc1 = new L.marker(e.latlng);      
               		map.addLayer(loc1);
		}
		else if (loc2 == null) {
        		loc2 = new L.marker(e.latlng);
			if (loc1.getLatLng () == loc2.getLatLng ())
				loc2 = null;
			else {
            			map.addLayer(loc2);
				getPaths();
			}
       		}
	};
}

/*
*Функция удаляющая отобращенные пути и метки на карте.
*/
function deletePaths(){
	if (loc1 != null && loc2 != null) {
		for(i in map._layers) {
        		if(map._layers[i]._path != undefined) {
           			try {
               				map.removeLayer(map._layers[i]);
            			}
           			 catch(e) {
                			console.log("error");
           			 }
       			}
   		}
		map.removeLayer(loc1);
		map.removeLayer(loc2);
		loc1 = null;
		loc2 = null;	
   	 }
}

/*
*Функция отображающая окно с информацией о маршруте.
*/
function showPathsInfo(){
}

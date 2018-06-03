/*
*Функция отображающая метки A и B - пункт отправления и пункт прибытия соответственно. 
*/
var loc1, loc2;
var polylines = {};
var colors = ["red", "blue", "green"];
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
*Функция отображающая пути и окно с информацией о пути.
*/
function showPathsInfo(paths){
	for (var i = 0; i <  paths.length; i++) {
			var path = paths[i];
			var points = [];
			for (var point of path.listPoints) {
					var lat = parseFloat(point.latitude.toString()) + i*0.00004,
					lon = parseFloat(point.longitude.toString()) ;
					points.push ([lat, lon]);
			}
			var route = "";
			for (var list of path.listRoutes) {
				route += 'Сесть на ' + list[0] + " " + list[1]+ ' на остановке "' + list[2] + '", высадиться на остановке "'+ list[3] + '"<br>';
			}
			polylines[i] = new L.Polyline(points, {color : colors[i], opacity : 1,weight : 5}).bindPopup(route);
			map.addLayer(polylines[i]);
			var len = points.length;
			var startPolyline = new L.Polyline([points[0], points[1]], {color : "grey", opacity : 1,weight : 5}).bindPopup(route);
			map.addLayer(startPolyline);
			var endPolyline = new L.Polyline([points[len - 1], points[len - 2]], {color : "grey", opacity : 1,weight : 5}).bindPopup(route);
			map.addLayer(endPolyline);
	}
}

/*
*Функция для отправки запроса на получение координат для отрисовки путей. 
* p1, p2 - координаты меток A и B.
*/
function getPaths() {
	var p1 = loc1.getLatLng(), p2 = loc2.getLatLng();
	$.post("http://www.testsite.loc/SMOT/server/main.php",{"name":"getPaths", 				
	"point1_lat":parseFloat(p1.lat.toString()), 
	"point1_lon":parseFloat(p1.lng.toString()),
	"point2_lat":parseFloat(p2.lat.toString()), 
	"point2_lon":parseFloat(p2.lng.toString())}, 
	function(data){	
		var paths = $.parseJSON(data);
		var colors = ["red", "blue", "green"];
		for (var i = 0; i <  paths.length; i++) {
			var path = paths[i];
			var points = [];
			for (var point of path.listPoints) {
				var lat = parseFloat(point.latitude.toString()) + i*0.00003,
				lon = parseFloat(point.longitude.toString()) ;
				points.push ([lat, lon]);
			}
			var route = "";
			for (var list of path.listRoutes) {
				route += 'Сесть на ' + list[0] + " "+list[1]+ ' на остановке "' + list[2] + '" ,высадиться на остановке "'+ list[3] + '"<br>';
			}
			polylines[i] = new L.Polyline(points, {color : colors[i], opacity : 1,weight : 5}).bindPopup(route);
			map.addLayer(polylines[i]);
		}
	});
}





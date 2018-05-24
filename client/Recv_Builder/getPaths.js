function sendPost() {
	var p1 = loc1.getLatLng(), p2 = loc2.getLatLng();
	$.post("http://www.testsite.loc/SMOT/server/main.php",{"name":"getPaths", 				
	"point1_lat":parseFloat(p1.lat.toString()), 
	"point1_lon":parseFloat(p1.lng.toString()),
	"point2_lat":parseFloat(p2.lat.toString()), 
	"point2_lon":parseFloat(p2.lng.toString())}, 
	function(data){
		if (polyline) {
			map.removeLayer(polyline);
		}	
		
		var paths = $.parseJSON(data);
		var colors = ["red", "blue", "green"];
		for (var i = 0; i <  paths.length; i++) {
			var path = paths[i];
			var points = [];
			for (var point of path.listPoints) {
				var lat = parseFloat(point.latitude.toString()),
				lon = parseFloat(point.longitude.toString());
				points.push ([lat, lon]);
			}
			var polyline = new L.Polyline(points, {color : colors[i]});
			map.addLayer(polyline);
		}
	});
}







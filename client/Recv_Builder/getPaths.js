function sendPost() {
	if (loc2 != null && loc1 != null) {
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
			var stops = $.parseJSON(data);
			for (var prop in stops) {
				for (var list in stops[prop].listPoints) {
					var lat = parseFloat(stops[prop].listPoints[list].latitude.toString()),
					lon = parseFloat(stops[prop].listPoints[list].longitude.toString());
					var ll= {lat,lon};

					//if(next_point == 0)
					//next_point = {lat,lon};
					//L.polyline([next_point, ll]).addTo(map).bindPopup("." + ll);
					//next_point = ll;
				}
			}
		
		});
       	}
  }

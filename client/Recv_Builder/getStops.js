$.post("http://www.testsite.loc/SMOT/server/main.php",{"name":"getStops"},
   		function(data){
     		var stops = $.parseJSON(data);
		//console.log(stops);
		for (var prop in stops) {
 		// console.log(prop + " : " + stops[prop]);
			var posLat = parseFloat(stops[prop][0].toString());
			var posLng = parseFloat(stops[prop][1].toString());
			L.marker([posLat, posLng]).addTo(map);
		}
});

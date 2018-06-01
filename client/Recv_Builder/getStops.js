$.post("http://www.testsite.loc/SMOT/server/main.php",{"name":"getStops"},
   		function(data){
     		var stops = $.parseJSON(data);
		for (var id in stops) {
			var posLat = parseFloat(stops[id][0].toString());
			var posLng = parseFloat(stops[id][1].toString());
			drawStops(posLat, posLng, id);		
		}
});




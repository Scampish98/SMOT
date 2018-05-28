$.post("http://www.testsite.loc/SMOT/server/main.php",{"name":"getStops"},
   		function(data){
     		var stops = $.parseJSON(data);
		//console.log(stops[2]);
		for (var prop in stops) {
			var posLat = parseFloat(stops[prop][0].toString());
			var posLng = parseFloat(stops[prop][1].toString());
			drawStops(posLat, posLng);		
		}
});


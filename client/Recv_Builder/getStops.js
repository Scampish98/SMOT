$.post("http://www.testsite.loc/SMOT/server/main.php",{"name":"getStops"},
   		function(data){
     		var stops = $.parseJSON(data);
		//console.log(stops[1]);
		for (var id in stops) {
			//console.log(id);
			var posLat = parseFloat(stops[id][0].toString());
			var posLng = parseFloat(stops[id][1].toString());
			drawStops(posLat, posLng, id);		
		}
});




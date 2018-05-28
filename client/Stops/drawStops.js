var stops = {};
function drawStops(lat, lng, id){
	var iconn = new L.icon({iconUrl:"http://www.testsite.loc/SMOT/client/stop1.png",
	iconAnchor : [25, 25]});
	stops[id] = L.marker([lat, lng], {icon : iconn}).addTo(map);
	stops[id].on("click", function (event) {
		sendStopInfo(id);	
		//console.log(id);	
	});
}

function showStopInfo(info,id){
	console.log(info);
	var str ="";
	for (var i in info) {
		str +=info[i][0]+" "+ info[i][1] + "  -  " + info[i][2] + "<br>";
	}
	stops[id].bindPopup(str).openPopup();
	//console.log (id);
	
}

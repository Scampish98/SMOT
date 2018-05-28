
function drawStops(lat, lng, id){
var iconn = new L.icon({iconUrl:"http://www.testsite.loc/SMOT/client/stop1.png",
	iconAnchor : [25, 25]});
	var stop = L.marker([lat, lng], {icon : iconn}).addTo(map);
	stop.on("click", function (event) {
   		console.log(id);
		sendStopInfo(id);
	});
}

function showStopInfo(lat, lng, id){}

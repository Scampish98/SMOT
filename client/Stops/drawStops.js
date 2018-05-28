
function drawStops(lat, lng){
var iconn = new L.icon({iconUrl:"http://www.testsite.loc/SMOT/client/stop1.png",
	iconAnchor : [25, 25]});
	L.marker([lat, lng], {icon : iconn}).addTo(map);
}
function showStopInfo() {


}

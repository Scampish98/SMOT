/*
*Функция для отобращения остановок на карте. 
*lat, lon - координаты остановки (широта и долгота соответственно).
*id - идентификатор остановки
*/
var stops = {};
function drawStops(lat, lng, id){
	var iconn = new L.icon({iconUrl:"http://www.testsite.loc/SMOT/client/stop1.png",
	iconAnchor : [25, 25]});
	stops[id] = L.marker([lat, lng], {icon : iconn}).addTo(map);
	stops[id].on("click", function (event) {
		sendStopInfo(id);	
	});
}

/*
*Функция для отобращения окна с информацией об остановке. 
*info - информация со списком маршрутов, которые останавливаются на этой остановке,
*ближайшим временемих прибытия.
*id - идентификатор остановки
*/
function showStopInfo(info,id){
	console.log(info);
	var str ="";
	for (var i in info) {
		str +=info[i][0]+" "+ info[i][1] + "  -  " + info[i][2] + "<br>";
	}
	stops[id].bindPopup(str).openPopup();
	
}

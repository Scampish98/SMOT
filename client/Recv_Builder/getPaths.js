/*
*Функция для отправки запроса на получение координат для отрисовки путей. 
* p1, p2 - координаты меток A и B.
*/
function getPaths() {
	var p1 = loc1.getLatLng(), p2 = loc2.getLatLng();
	$.post("http://www.testsite.loc/SMOT/server/main.php",{"name":"getPaths", 				
	"point1_lat":parseFloat(p1.lat.toString()), 
	"point1_lon":parseFloat(p1.lng.toString()),
	"point2_lat":parseFloat(p2.lat.toString()), 
	"point2_lon":parseFloat(p2.lng.toString())}, 
	function(data){	
		var paths = $.parseJSON(data);
		showPathsInfo(paths);
	});
}





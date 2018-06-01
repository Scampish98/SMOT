//Определяем карту, координаты центра и начальный масштаб
var map = L.map('map',{zoomControl:false}).setView([61.77,34.40], 14);

//Добавляем на карту слой OpenStreetMap
L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);


btnDraw();


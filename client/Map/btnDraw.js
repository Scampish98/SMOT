var loc1, loc2;
map.on('click', function(e) {
	onMapClick(e);
});
function onMapClick(e) {
	if (loc1 == null) {
         	loc1 = new L.marker(e.latlng, {draggable: 'true'});
         	loc1.on('dragend', function(event) {
			sendPost();
		});
                map.addLayer(loc1);
	}
	else if (loc2 == null) {
        	loc2 = new L.marker(e.latlng, {draggable: 'true'});
            	loc2.on('dragend', function(event) {
			sendPost();
           	});
            	map.addLayer(loc2);
	    	sendPost();
        }
};
var polyline;
var next_point=0;

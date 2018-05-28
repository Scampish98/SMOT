var loc1, loc2;
function clickGetPaths(){
    if (loc1 != null && loc2 != null) {
	//deletePaths();
	for(i in map._layers) {
        	if(map._layers[i]._path != undefined) {
           		try {
               			map.removeLayer(map._layers[i]);
            		}
           		 catch(e) {
                		console.log("error");
           		 }
       		}
   	}
	map.removeLayer(loc1);
	map.removeLayer(loc2);
	loc1 = null;
	loc2 = null;
	
    }
	map.on('click', onMapClick);
	function onMapClick(e) {
		if (loc1 == null) {
         		loc1 = new L.marker(e.latlng);      
               		map.addLayer(loc1);
		}
		else if (loc2 == null) {
        		loc2 = new L.marker(e.latlng);
			if (loc1.getLatLng () == loc2.getLatLng ())
				loc2 = null;
			else {
            			map.addLayer(loc2);
				sendPost();
			}
       		}
		//if (loc1 != null && loc2 != null) 
			//sendPost();
	};
}

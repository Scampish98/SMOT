var routesName=[];
function routeInfo(routes){
	for (var route in routes) {
		routesName[route.toString()] = 	routes[route][0].toString() + " " +routes[route][1].toString();
		var btn = document.createElement("input");
		btn.id = 'route' + route.toString();
		btn.type = 'button';
		btn.value = routes[route][0].toString() + " " +routes[route][1].toString()
		btn.setAttribute('onclick', 'clickOnBtn('+route.toString()+');');
    		document.getElementById("button-wrapper").appendChild(btn);	
	}
}
function clickOnBtn(id){
	//console.log(id);	
	$.post("http://www.testsite.loc/SMOT/server/main.php",{"name":"getRouteInfo","id":id},
   		function(data){
  			var routeInfo = $.parseJSON(data);
			//console.log(routeInfo.interval);	
			//console.log(routeInfo.map);
			//console.log(routeInfo);
			var name1, name2, name3;
			for (var prop in routeInfo) {
				if(name1 == null)
					name1 = prop;
				else if(name2 == null)
					name2 = prop;
			}
			viewInfo(id);
	});
}
function viewInfo(id){
	if(document.getElementById('routes-info') != null){
		clickClose();
	}
	//console.log(routesName[id]);
	var str = '<div id="routes-info">'+
     	 '<h2 id="Heading"  align="center">'+ routesName[id]+'</h2>'+
     	 '<div id="bodyContent" align="center">'+
     	 '<p>blablablablablablablablabla</p>'+
     	 '<p>blablablabla</p>'+
     	 '</div>'+
      	'</div>';
	var show = document.createElement('div');
	show.innerHTML = str; 
	document.body.appendChild(show);

	var btn = document.createElement("input");
	btn.id = 'close' ;
	btn.type = 'button';
	btn.value = "Close";
	btn.setAttribute('onclick', 'clickClose();');
	document.body.appendChild(btn);

}
function clickClose(){
	var el = document.getElementById('routes-info');
	el.parentNode.removeChild(el);
	el = document.getElementById('close');
	el.parentNode.removeChild(el);
}


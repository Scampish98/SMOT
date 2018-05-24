$.post("http://www.testsite.loc/SMOT/server/main.php",{"name":"getRoutes"},
   		function(data){
  			var routes = $.parseJSON(data);
			for (var route in routes) {	
			var btn = document.createElement("input");
			btn.id = 'route' + route.toString();
			btn.type = 'button';
			btn.value = routes[route][0].toString() + " " +routes[route][1].toString()
			btn.setAttribute('onclick', 'click('+route.toString()+');');
    			document.getElementById("button-wrapper").appendChild(btn);
			
		}
});

$.post("http://www.testsite.loc/SMOT/server/main.php",{"name":"getRoutes"},
   		function(data){
  			var routes = $.parseJSON(data);
			//console.log(routes);	
			routeInfo(routes);		
});

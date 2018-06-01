/*
*Функция для отправки запроса на получение информации о маршруте.
*id - индекс маршрута. 
*/
function getRouteInfo(id){
	$.post("http://www.testsite.loc/SMOT/server/main.php",{"name":"getRouteInfo","id":id},
   		function(data){
  			var routeInfo = $.parseJSON(data);
			var name1, name2;
			for (var prop in routeInfo) {
				if(name1 == null)
					name1 = prop;
				else if(name2 == null)
					name2 = prop;
			}
			viewInfo(id,name1,name2,routeInfo);
	});
}

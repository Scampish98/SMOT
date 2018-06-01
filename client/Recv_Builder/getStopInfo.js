/*
*Функция для отправки запроса на получение информации об остановке.
*id - индекс остановки. 
*/
function sendStopInfo(id) {
	$.post("http://www.testsite.loc/SMOT/server/main.php",{"name":"getStopInfo", "id":id},
   		function(data){
  			var info = $.parseJSON(data);
			showStopInfo(info,id);
});
}

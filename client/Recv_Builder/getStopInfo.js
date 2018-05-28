function sendStopInfo(id) {
	$.post("http://www.testsite.loc/SMOT/server/main.php",{"name":"getStopInfo", "id":id},
   		function(data){
  			var info = $.parseJSON(data);
			console.log(info);
			for (var id in info) {
				//showStopInfo();
				console.log(info[id]);	
			}
});
}

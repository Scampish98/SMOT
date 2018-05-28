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
			viewInfo(id,name1,name2,routeInfo);
	});
}
function viewInfo(id ,name1,name2,info){
	if(document.getElementById('routes-info') != null){
		clickClose();
	}
	//console.log(routesName[id]);
	var tbl1 = '<table border = "1"  width="500" >'+
  '<tr>'+
   '<th rowspan="2" class="first">Конечный пункт</th>'+
   '<th colspan="3">Время начала движения</th>'+
   '<th colspan="3">Время окончания движения</th>'+
 '</tr>'+
 '<tr>'+
  ' <td class="first">Будни</td>'+
   '<td class="first">Сб</td>'+
   '<td class="first">Вс</td>'+
   '<td class="first">Будни</td>'+
   '<td class="first">Сб</td>'+
   '<td class="first">Вс</td>'+
 '</tr>'+
 '<tr align = "center">'+
  '<td rowspan="1" class="first">'+name1+'</td>'+
  '<td>'+check(info[name1][0][0])+'</td>'+
  '<td>'+check(info[name1][0][1])+'</td>'+
  '<td>'+check(info[name1][0][2])+'</td>'+
  '<td>'+check(info[name1][1][0])+'</td>'+
  '<td>'+check(info[name1][1][1])+'</td>'+
  '<td>'+check(info[name1][1][2])+'</td>'+
  '</tr>'+
'<tr align = "center">'+
  '<td rowspan="1" class="first">'+name2+'</td>'+
  '<td>'+check(info[name2][0][0])+'</td>'+
  '<td>'+check(info[name2][0][1])+'</td>'+
  '<td>'+check(info[name2][0][2])+'</td>'+
  '<td>'+check(info[name2][1][0])+'</td>'+
  '<td>'+check(info[name2][1][1])+'</td>'+
  '<td>'+check(info[name2][1][2])+'</td>'+
  '</tr>'+
 '</table>';
	var tbl2 = '<table border = "1"  width="500" >' +
'<caption>Интервал движения</caption>'+
  '<tr>'+
    '<th >Будни</th>'+
   '<th >Сб</th>'+
   '<th >Вс</th>'+
 '</tr>'+

'<tr align = "center">'+
 '<td>'+ check(info.interval[0][0]) +"-" + check(info.interval[0][1]) + '</td>'+
 '<td>'+ check(info.interval[1][0]) +"-" + check(info.interval[1][1]) + '</td>'+
 '<td>'+ check(info.interval[2][0]) +"-" + check(info.interval[2][1]) + '</td>'+

 '</tr>'+
 '</table>';

	var str = '<div id="routes-info">'+
     	 '<h3 id="Heading"  align="center">'+ routesName[id]+'</h2>'+
     	 '<div id="bodyContent" align="center">'+
     	 tbl1+ tbl2+
     	 '</div>'+	
      	'</div>';
	var show = document.createElement('div');
	show.innerHTML = str; 
	document.body.appendChild(show);
	var img= '<div id = "img"><img src='+info.map.toString()+ ' width="400px" ></div>';
	var show = document.createElement('div');
	show.innerHTML = img; 
	document.body.appendChild(show);
	var btn = document.createElement("input");
	btn.id = 'close' ;
	btn.type = 'button';
	btn.value = "X";
	btn.setAttribute('onclick', 'clickClose();');
	document.body.appendChild(btn);
	//console.log(info[name1][0][0].toString());

}
function clickClose(){
	var el = document.getElementById('routes-info');
	el.parentNode.removeChild(el);
	el = document.getElementById('close');
	el.parentNode.removeChild(el);
	el = document.getElementById('img');
	el.parentNode.removeChild(el);
}
function check(e){

if (e == "10000000") 
	return "";
else if (e == "23:59:00")
	return "-";
else return e;


}


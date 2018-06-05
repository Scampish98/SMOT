/*
*Функция для отобращения окна с названиями маршрут. 
*routes - данные содержащие названия маршрутов и информацию о них.
*/
var routesName=[];
function routeInfo(routes){
	for (var route in routes) {
		routesName[route.toString()] = 	routes[route][0].toString() + " " +routes[route][1].toString();
		var btn = document.createElement("input");
		btn.id = 'route';
		btn.setAttribute("class", "route");
		btn.type = 'button';
		btn.value = routes[route][0].toString() + " " +routes[route][1].toString()
		btn.setAttribute('onclick', 'getRouteInfo('+route.toString()+');');
    		document.getElementById("button-wrapper").appendChild(btn);	
	}
}
/*
*Функция для отображения окна с информацией о маршруте. 
*id - идентификатор маршрута
*name1, name2 - конечные остановки маршрута
*info - информация о маршруте
*/

function viewInfo(id ,name1,name2,info){
	if(document.getElementById('routes-info') != null){
		clickClose();
	}
	var tbl1 = '<table id = "tab1" border = "1">'+
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
 '<tr >'+
  '<td rowspan="1" class="first">'+name1+'</td>'+
  '<td>'+check(info[name1][0][0]).substring(0,5)+'</td>'+
  '<td>'+check(info[name1][0][1]).substring(0,5)+'</td>'+
  '<td>'+check(info[name1][0][2]).substring(0,5)+'</td>'+
  '<td>'+check(info[name1][1][0]).substring(0,5)+'</td>'+
  '<td>'+check(info[name1][1][1]).substring(0,5)+'</td>'+
  '<td>'+check(info[name1][1][2]).substring(0,5)+'</td>'+
  '</tr>'+
'<tr >'+
  '<td rowspan="1" class="first">'+name2+'</td>'+
  '<td>'+check(info[name2][0][0]).substring(0,5)+'</td>'+
  '<td>'+check(info[name2][0][1]).substring(0,5)+'</td>'+
  '<td>'+check(info[name2][0][2]).substring(0,5)+'</td>'+
  '<td>'+check(info[name2][1][0]).substring(0,5)+'</td>'+
  '<td>'+check(info[name2][1][1]).substring(0,5)+'</td>'+
  '<td>'+check(info[name2][1][2]).substring(0,5)+'</td>'+
  '</tr>'+
 '</table>';
	var tbl2 = '<table id = "tab2" border = "1">' +
'<caption>Интервал движения</caption>'+
  '<tr>'+
    '<th >Будни</th>'+
   '<th >Сб</th>'+
   '<th >Вс</th>'+
 '</tr>'+

'<tr align = "center">'+
 '<td>'+ check(info.interval[0][0]) + "-" + check(info.interval[0][1]) + '</td>'+
 '<td>'+ check(info.interval[1][0]) + "-" + check(info.interval[1][1]) + '</td>'+
 '<td>'+ check(info.interval[2][0]) + "-" + check(info.interval[2][1]) + '</td>'+

 '</tr>'+
 '</table>';

	var str = '<div id="routes-info">'+
     	 '<h3 id="Heading" >'+ routesName[id]+'</h2>'+
     	 tbl1 + tbl2 + '<div id="img"><img src="'+info.map.toString()+ '" width = "97%" ></div>'+	
      	'</div>';
	var show = document.createElement('div');
	show.innerHTML = str; 
	document.body.appendChild(show);
	/*var img= '<div id = "img"><img src='+info.map.toString()+ ' width = "99%"></div>';
	var show = document.createElement('div');
	show.innerHTML = img; */
	//document.body.appendChild(show);
	var btn = document.createElement("input");
	btn.id = 'close' ;
	btn.type = 'button';
	btn.value = "X";
	btn.setAttribute('onclick', 'clickClose();');
	document.getElementById("routes-info").appendChild(btn);	
}
/*
*Функция для удаления окна с информацией о маршруте. 
*/
function clickClose(){
	var el = document.getElementById('routes-info');
	el.parentNode.removeChild(el);
}
function check(e){

	if (e == "10000000") 
		return "";
	else if (e == "23:59:00")
		return "-";
	else return e;

}


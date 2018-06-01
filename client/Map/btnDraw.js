/*
*Функция отображающая кнопку "Проложить путь" на карте.
*/

function btnDraw() {
	var btnp = document.createElement("input");
	btnp.id = 'btnGetPaths';
	btnp.type = 'button';
	btnp.value = "Проложить путь";
	btnp.setAttribute('onclick', 'drawPaths();');
	document.getElementById("button-getPaths").appendChild(btnp);
}

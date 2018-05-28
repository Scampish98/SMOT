<?php

//header ('Access-Control-Allow-Origin: *'); 

//Подключаем все функции
require_once ('classes.php');
require_once ('DB_recv/db.php');
require_once ('Compute/compute.php');

/*
 * Функция для обработки запроса расчета путей.
 * В качестве входных данных принимает координаты пунктов отправления и прибытия,
 * которые хранятся в массиве $_POST с ключами point1_lat, point1_lon, point2_lat и point2_lon
 * Возвращает json-строку, содержащую информацию о путях.
 * Информация представляется в виде массива, каждый элемент которого содержит элемент класса path.
 * Класс path содержит два массива listPoints и listRoutes.
 * listPoints содержит последовательность элементов класса __point, имеющего поля latitude и longitude,
 * в виде массива
 * $listRoutes хранит последовательность пересадок в виде массива, каждый элемент которого
 * является массивом из четырех элементов: 
 * ['тип транспорта', 'номер транспорта', 'название остановки посадки', 'название остановки высадки']
 */ 
function handlerPaths () {
  $p1 = new point (["floatPointLat" => $_POST["point1_lat"], "floatPointLon" => $_POST["point1_lon"]]);
  $p2 = new point (["floatPointLat" => $_POST["point2_lat"], "floatPointLon" => $_POST["point2_lon"]]);
  $paths = getPaths ($p1, $p2);
  return json_encode ($paths, JSON_UNESCAPED_UNICODE);
}

/*
 * Функция для обработки запроса информации об остановке.
 * В качестве входных данных принимает идентификатор остановки для запроса.
 * Хранится идентификатор в $_POST["id"] 
 * Возвращает json-строку, содержающую информацию об остановке (какие маршруты через нее проходят 
 * и через следующий момент времени, когда они предположительно придут на остановку).
 * Информация представляется в виде массива, каждый элемент которого является массивом
 * из 3-х элементов:
 * ['тип транспорта', 'номер транспорта', 'предположительное время прибытия']
 */
function handlerStopInfo () {
  $tmp = getRoutesByStopId ($_POST["id"]);
	$routes = [];
	foreach ($tmp as $route1) {
		$f = true;
		foreach ($routes as $route2)
			if ($route1 == $route2)
				$f = false;
		if ($f)
			$routes[] = $route1;
	}

  $res = [];
  foreach ($routes as $route) {
    $res[] = [$route -> strRouteType, $route -> intRouteNum, getNextTime ($_POST["id"], $route -> intRouteId, getCurTime ())];
  }
  return json_encode ($res, JSON_UNESCAPED_UNICODE);
}

/*
 * Функция для обработки запроса информации об маршруте.
 * В качестве входных данных принимает идентификатор маршрута для запроса.
 * Хранится идентификатор в $_POST["id"] 
 * Возвращает json-строку, содержающую информацию о маршруте (расписание маршрутов
 * и url картинки со статической картой).
 * Информация представляется в виде ассоциативного массива, 
 * В этом массиве хранятся значения для четырех ключей:
 * название одной конечной остановки (name1), название второй конечной остановки (name2),
 * "interval" - интервал движения, "map" - url картинки.
 * Для ключей name1 и name2 хранится массив из двух элементов:
 * Первый элемент - время начала движения маршрута, содержит массив из трех элементов
 * ['время в будни', 'время в субботу', 'время в воскресенье']
 * Второй элемент - время окончания движения маршрута, содержит аналогичный первому элементу массив
 * Для ключа "interval" - интервал движения маршрута, хранится массив из трех элементов
 * ['интервал в будни', 'интервал в субботу', 'интервал в воскресенье']
 * Для ключа "map" хранится строка, содержащая url картинки
 */
function handlerRouteInfo () {
  $route = getObject ("route", $_POST["id"]);
  $stop1 = getStopByNum (0, $_POST["id"], 0);
  $stop2 = getStopByNum (0, $_POST["id"], 1);
  $res[$stop1 -> strStopName] = [[$route -> timeRouteStartWeekOne, $route -> timeRouteStartSatOne,
				  $route -> timeRouteStartSunOne],
				 [$route -> timeRouteEndWeekOne, $route -> timeRouteEndSatOne,
				  $route -> timeRouteEndSunOne]];
  $res[$stop2 -> strStopName] = [[$route -> timeRouteStartWeekTwo, $route -> timeRouteStartSatTwo,
				  $route -> timeRouteStartSunTwo],
				 [$route -> timeRouteEndWeekTwo, $route -> timeRouteEndSatTwo,
				  $route -> timeRouteEndSunTwo]];
  $res["interval"] = [[$route -> intRouteIntervalLeftWeek, $route -> intRouteIntervalRightWeek], 
		      [$route -> intRouteIntervalLeftSat, $route -> intRouteIntervalRightSat], 
		      [$route -> intRouteIntervalLeftSun, $route -> intRouteIntervalRightSun]];
  $res["map"] = $route -> strRouteMapUrl;
  return json_encode ($res, JSON_UNESCAPED_UNICODE);
}

/*
 * Функция для обработки запроса на список маршрутов
 * Данная функция не имеет входных данных
 * Возвращает json-строку со списком маршрутов в виде ассоциативного массива:
 * ключ - id маршрута
 * значение - массив ['тип маршрута', 'номер маршрута']
 */ 
function handlerRoutes () {
  $routes = getRouteIds ();
  $res = [];
  foreach ($routes as $idRoute) {
    $route = getObject ("route", $idRoute);
    $res[$idRoute] = [$route -> strRouteType, $route -> intRouteNum];
  }
  return json_encode ($res, JSON_UNESCAPED_UNICODE);
}

/*
 * Функция для обработки запроса на список остановок и их координаты
 * Данная функция не имеет входных данных
 * Возвращает json-строку со списком остановок и их координатами в виде ассоциативного массива:
 * ключ - id остановки
 * значение - массив ['широта', 'долгота']
 */ 

function handlerStops () {
  $stops = getPointsByStops ();
  $res = [];
  foreach ($stops as $stop => $point) {
    $res[$stop] = [$point -> floatPointLat, $point -> floatPointLon];
  }
  return json_encode ($res, JSON_UNESCAPED_UNICODE);
}



/*$_POST["name"] = "getStopInfo";
$_POST["id"] = 40;*/

/*
 * Выбор обработчика запроса в зависимости от вида запроса (определяется через $_POST["name"]
 */ 
switch ($_POST["name"]) {
  case "getPaths":
    echo handlerPaths ();
    break;
  case "getStopInfo":
    echo handlerStopInfo ();
    break;
  case "getRouteInfo":
    echo handlerRouteInfo ();
    break;
  case "getRoutes":
    echo handlerRoutes ();
    break;
  case "getStops":
    echo handlerStops ();
    break;
}
?>

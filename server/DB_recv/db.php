<?php

const host = '127.0.0.1';
const user = 'root';
const password = 'pass';

/*
 * Подключение к MySQL.
 * Выбор базы данных
 */
$db = mysqli_connect (host, user, password);
mysqli_select_db ($db, 'smot');
mysqli_set_charset ($db, 'utf8');
mysqli_query ($db, 'SET NAMES utf8');

/*
 * Функция, очищающая БД.
 * Удаляет существующую БД.
 * Создает БД с именем dbName
 * Вызывает mysqli_select_db для выбора этой БД
 * Запускает create () для инициализации БД
 */
function clear () {
	global $db;
	mysqli_query ($db, 'DROP DATABASE IF EXISTS smot');
	mysqli_query ($db, 'CREATE DATABASE smot CHARACTER SET utf8 COLLATE utf8_general_ci');
	mysqli_select_db ($db, 'smot');
	require ('create.php');
}

/*
 * Функция для добавления записи в БД в таблицы tblPoint, tblStop, tblRoute, tblPointRoute, tblStopRoute.
 * Принимает параметр $obj - объект класса point, stop, route, pointRoute или stopRoute.
 * Определяет класс, к которому относится $obj
 * Составляет INSERT запрос, используя статическое свойство $order у класса.
 * Отправляет запрос БД.
 * Возвращает значение первичного ключа добавленного элемента.
 */
function insert ($obj) {
	global $db;
	$type = get_class ($obj);
	$type = strtoupper (substr ($type, 0, 1)) . substr ($type, 1, strlen ($type) - 1);
	$table = 'tbl' . $type;
	$query = 'INSERT INTO ' . $table . ' (';
	$values = '';
	foreach ($type::$order as $field) {
		if ($field === 'int' . $type . 'Id')
			continue;
		$tmp = $obj -> {$field};
		if (is_string ($tmp))
			$tmp = '"' . $tmp . '"';
		$query = $query . $field . ', ';
		$values = $values . $tmp . ', ';
	}
	$query = substr ($query, 0, strlen ($query) - 2) . ')';
	$values = '(' . substr ($values, 0, strlen ($values) - 2) . ');';
	$query = $query . ' VALUES ' . $values;

	echo $query . "\n";
	mysqli_query ($db, $query);
	return mysqli_insert_id ($db);
}

/*
 * Функция возвращающая объект класса $type с идентификатором в БД $id.
 * Реализована для классов point, stop и route.
 * Принимает в качестве параметров строку $type - название класса и идентификатор в БД $id.
 * Возвращает искомый объект класса $type. 
 */
function getObject ($type, $id) {
	global $db;
	$tmp = strtoupper (substr ($type, 0, 1)) . substr ($type, 1, strlen ($type) - 1);
	$table = 'tbl' . $tmp;
	$tmp = "SELECT * FROM " . $table . " WHERE int" . $tmp . "Id = " . $id;
	$query = mysqli_query ($db, $tmp);
	return new $type (mysqli_fetch_assoc ($query));
}

/*
 * Функция, находящая точку, соответствующую остановке $id.
 * Принимает в качестве параметра идентификатор остановки $id
 * Возвращает объект класса point - искомую точку.
 */ 
function getPointByStopId ($id) {
	global $db;
	$stop = getObject ("stop", $id);
	return getObject ("point", $stop -> intPointId);
}

/*
 * Функция для получения всех идентификаторов остановок.
 * Возвращает индексный массив, содержащий идентификаторы остановок.
 */ 
function getStopIds () {
	global $db;
	$query = "SELECT intStopId FROM tblStop";
	$query_res = mysqli_query ($db, $query);
	$res = [];
	while ($tmp = mysqli_fetch_assoc ($query_res))
		$res[] = $tmp["intStopId"];
	return $res;
}

/*
 * Функция для получения соответствий остановка -> точка.
 * Возвращает массив, в котором ключ - идентификатор остановки, а значение - соответствующая ей точка.
 */ 
function getPointsByStops () {
	$stops = getStopIds ();
	$res = [];
	foreach ($stops as $stop) {
		$res[$stop] = getPointByStopId ($stop);
	}
	return $res;
}

/*
 * Функция для получения списка маршрутов, проходящих через остановку $id.
 * Получает в качестве параметра идентификатор остановки $id.
 * Возвращает индексный массив, каждый элемент которого - маршрут, проходящий через остановку $id 
 * (объект класса route).
 */ 
function getRoutesByStopId ($id) {
	global $db;
	$query = "SELECT intRouteId FROM tblStopRoute WHERE intStopId = " . $id;
	$query_res = mysqli_query ($db, $query);
	$res = [];
	while ($tmp = mysqli_fetch_assoc ($query_res))
		$res[] = getObject ("route", $tmp["intRouteId"]);
	return res;
}

/*
 * Функция для получения маршрута в виде последовательности остановок.
 * Принимает в качестве параметров идентификатор маршрута $id и направление, по которому требуется его получить, $dir.
 * Возвращает индексный массив, содержащий последовательность остановок (объекты класса stop), представляющих маршрут $id
 * в направлении $dir.
 */ 
function getRouteAsStops ($id, $dir) {
	global $db;
	$query = "SELECT intStopId FROM tblStopRoute WHERE intRouteId = " . $id . " and boolDirection = " . $dir;
	$query_res = mysqli_query ($db, $query);
	$res = [];
	while ($tmp = mysqli_fetch_assoc ($query_res))
		$res[] = getObject ("stop", $tmp["intStopId"]);
	return $res;
}

/*
 * Функция для получения маршрута в виде последовательности точек.
 * Принимает в качестве параметра идентификатор маршрута $id.
 * Возвращает индексный массив, содержащий последовательность точек (объекты класса point), представляющих маршрут.
 */ 
function getRouteAsPoints ($id, $dir) {
	global $db;
	$query = "SELECT intPointId FROM tblPointRoute WHERE intRouteId = " . $id . " and boolDirection = " . $dir;
	$query_res = mysqli_query ($db, $query);
	$res = [];
	while ($tmp = mysqli_fetch_assoc ($query_res))
		$res[] = getObject ("point", $tmp["intPointId"]);
	return $res;
}

/*
 * Функция для получения порядкового номера точки $idPoint в маршруте $idRoute в направлении $dir.
 * Принимает в качестве параметров идентификатор точки $idPoint, идентификатор маршрута $idRoute, направление $dir.
 * Возвращает требуемый порядковый номер точки. 
 */ 
function getNumPointInRoute ($idPoint, $idRoute, $dir) {
	global $db;
	$query = "SELECT intPointNum FROM tblPointRoute WHERE intRouteId = " . $idRoute . " and boolDirection = " . $dir .
	 " and intPointId = " . $idPoint;
	$query_res = mysqli_query ($db, $query);
	if (mysqli_num_rows ($query_res) == 0)
		return -1;
	$tmp = mysqli_fetch_assoc ($query_res);
	return $tmp['intPointNum'];
}

/*
 * Функция для получения порядкового номера остановки $idStop в маршруте $idRoute в направлении $dir.
 * Принимает в качестве параметров идентификатор остановки $idStop, идентификатор маршрута $idRoute, направление $dir.
 * Возвращает требуемый порядковый номер остановки. 
 */ 
function getNumStopInRoute ($idStop, $idRoute, $dir) {
	global $db;
	$query = "SELECT intStopNum FROM tblStopRoute WHERE intRouteId = " . $idRoute . " and boolDirection = " . $dir .
	 " and intStopId = " . $idStop;
	$query_res = mysqli_query ($db, $query);
	if (!mysqli_num_rows ($query_res))
		return -1;
	$tmp = mysqli_fetch_assoc ($query_res);
	return $tmp['intStopNum'];
}

/*
 * Функция для получения куска маршрута в виде последовательности точек между двумя остановками.
 * Принимает в качестве параметров идентификатор первой по порядку остановки $idFirstStop, второй по порядку $idSecondStop,
 * идентификатор маршрута $idRoute и рассматриваемое направление $dir.
 * Возвращает массив, каждый элемент которого является объектом класса point, - требуемый отрезок маршрута.
 */ 
function getRouteBetweenStops ($idFirstStop, $idSecondStop, $idRoute, $dir) {
	$route = getRouteAsPoints ($idRoute, $dir);
	$p1 = getPointByStopId ($idFirstStop);
	$p2 = getPointByStopId ($idSecondStop);
	$start = getNumPointInRoute ($p1 -> intPointId, $idRoute, $dir);
	$finish = getNumPointInRoute ($p2 -> intPointId, $idRoute, $dir);
	$res = [];
	for ($i = $start; $i <= $finish; $i++) {
		$res[] = $route[$i];
	}
	return $res;
}

/*
 * Функция для получения всех идентификаторов маршрутов.
 * Возвращает индексный массив, содержащий идентификаторы маршрутов.
 */ 
function getRouteIds () {
	global $db;
	$query = "SELECT intRouteId FROM tblRoute";
	$query_res = mysqli_query ($db, $query);
	$res = [];
	while ($tmp = mysqli_fetch_assoc ($query_res))
		$res[] = $tmp["intRouteId"];
	return $res;
}

/*
 * Функция для получения остановки с порядковым номером $numStop в маршруте $idRoute в направлении $dir.
 * Принимает как параметры порядковый номер остановки $numStop, идентификатор маршрута $idRoute и требуемое направление $dir.
 * Возвращает объект класса stop. 
 */ 
function getStopByNum ($numStop, $idRoute, $dir) {
	global $db;
	$query = "SELECT intStopId FROM tblStopRoute WHERE intStopNum = " . $numStop . " and intRouteId = " . $idRoute . 
	" and boolDirection = " . $dir;
	$query_res = mysqli_query ($db, $query);
	$tmp = mysqli_fetch_assoc ($query_res);
	return getObject ('stop', $tmp['intStopId']);
}

?>

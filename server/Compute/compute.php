<?php

const EPS = 1e-9; // константа для сравнения чисел в плавающей точкой


/*
 *  Точка старта для компаратора comp.
 */
$start = new point ();

/*
 * Компаратор для сортировки точек в порядке возрастания расстояния до точки $start.
 */
function comp ($a, $b) {
	global $start;
	$d1 = $start -> dist ($a);
	$d2 = $start -> dist ($b);
	return ($d1 - $d2) > EPS;
}

/*
 * Функция для получения ближайших от точки $p пяти остановок.
 * Получает в качестве параметра элемент класса point: $p - точка, для которой требуется найти ближайшие пять остановок
 * Получает данные обо всех остановках из БД
 * Сортирует все остановки по расстоянию от точки $p и возвращает массив из первых пяти в этом списке
 */
function getNearStops ($p, $col) {
	global $start;
	$res = [];
	$stops = getPointsByStops ();
	$start = $p;
	uasort ($stops, 'comp');
	foreach ($stops as $stop => $point) {
		$res[] = getObject ("stop", $stop);
		if (count ($res) == $col)
			break;
	}
	return $res;    
}

/*
 * Функция для получения информации о времени начала движения маршрута,
 * времени окончания движения маршрута.
 * Получает в качестве параметров переменные по ссылке, которые необходимо заполнить:
 * $S - время начала движения маршрута, $E - время окончания движения маршрута,
 * $l - левая граница интервала движения, $r - правая граница интервала движения.
 * Также как параметр передается идентификаторе маршрута $idRoute.
 */
function getTimeAndInterval (&$S, &$E, &$l, &$r, $idRoute, $dir) {
	$route = getObject ("route", $idRoute);

	$time = (string) date ("w", time ());
	if ($time == "0") {
		if (!$dir) {
			$S = $route -> timeRouteStartSunOne;
			$E = $route -> timeRouteEndSunOne;
		} else {
			$S = $route -> timeRouteEndSunTwo;
			$E = $route -> timeRouteEndSunTwo;
		}
		$l = $route -> intRouteIntervalLeftSun;
		$r = $route -> intRouteIntervalRightSun;
	} else if ($time == "6") {
		if (!$dir) {
			$S = $route -> timeRouteStartSatOne;
			$E = $route -> timeRouteEndSatOne;
		} else {
			$S = $route -> timeRouteEndSatTwo;
			$E = $route -> timeRouteEndSatTwo;
		}
		$l = $route -> intRouteIntervalLeftSat;
		$r = $route -> intRouteIntervalRightSat;
	} else {
		if (!$dir) {
			$S = $route -> timeRouteStartWeekOne;
			$E = $route -> timeRouteEndWeekOne;
		} else {
			$S = $route -> timeRouteEndWeekTwo;
			$E = $route -> timeRouteEndWeekTwo;
		}
		$l = $route -> intRouteIntervalLeftWeek;
		$r = $route -> intRouteIntervalRightWeek;
	}
}

/*
 * Функция для получения текущего времени в формате "HH.MM" (использует функцию date)
 */ 
function getCurTime () {
	return (string) date ("H.i", time ());
}

/*
 * Функция для конвертации времени из формата "HH.MM" в минуты.
 */ 
function timeFromString ($time) {
	return (int) substr ($time, 0, 2) * 60 + (int) substr ($time, 3, 2);
}

/*
 * Функция для конвертации времени из минут в "HH.MM". 
 */ 
function timeToString ($time) {
	return (string) floor ($time / 60.0) . (string) $time % 60;
}


/*
 * Реализует алгоритм из пункта 5.1
 * Получает в качестве параметров идентификатор остановки ($idStop) и идентификатор маршрута ($idRoute) в БД,
 * а также $T - текущее время в формате "HH.MM".
 * Возвращает требуемое время строкой в стандартном формате "HH.MM"
 */
function getNextTime ($idStop, $idRoute, $T) {
	$dir = 0;
	if (($num = getNumStopInRoute ($idStop, $idRoute, 0)) == -1) {
		$num = getNumStopInRoute ($idStop, $idRoute, 1);
		$dir = 1;
	}
	getTimeAndInterval ($S, $E, $l, $r, $idRoute, $dir);
	$S = timeFromString ($S);
	$E = timeFromString ($E);
	$T = timeFromString ($T);
	$mid = ($l + $r) * 0.5;
	$i = max (0, ceil ((float) ($T - $S - $num * $mid) / $mid));
	if ($i > floor (($E - $S) / $mid))
		$i = 0;
	$res = $S + $i * $mid + $num * $mid;
	return timeToString ($res);
}

/*
 * Функция для восстановления пути от $s до $f по массиву предков и упаковки этого пути в объект path.
 * Принимает в качестве параметров: массив предков $par, стартовую и финишную точки $start и $finish, 
 * Номера стартовой и финишной точек в графе $s и $f.
 * Возвращает полученный объект типа path.
 */ 
function getPathByPar ($par, $start, $finish, $s, $f) {
	$points[] = [new __point ($finish)];
	$res = new path ();

	for ($to = $par[$f]["v"]; $par[$to]["v"] != $s; $to = $par[$to]["v"]) {
		$v = $par[$to]["v"];
		$route = getObject ("route", $par[$to]["route"]);
		$stop1 = getObject ("stop", $v);
		$stop2 = getObject ("stop", $to);
		
		$part = getRouteBetweenStops ($v, $to, $par[$to]["route"], $par[$to]["dir"]);
		$tmp = [];
		foreach ($part as $point)
			$tmp[] = new __point ($point);
		$points[] = $tmp;
		$res -> listRoutes[] = [$route -> strRouteType, $route -> intRouteNum, $stop1 -> strStopName, $stop2 -> strStopName];
	}
	$points[] = [new __point ($start)];
	$res -> listRoutes = array_reverse ($res -> listRoutes);
	$points = array_reverse ($points);
	$res -> listPoints = [];
	foreach ($points as $a)
		$res -> listPoints = array_merge ($res -> listPoints, $a);
	return $res;
}

/*
 * Функция для сортировки двух пар (массивов из двух элементов)
 * сначала по первому элементу, затем по второму.
 */ 
function pairLess ($a, $b) {
	if ($a[0] == $b[0])
		return $a[1] < $b[1];
	return $a[0] < $b[0];
}

/*
 * Функция делает расчет пути от точки $start до точки $finish по алгоритму из пункта 5.3.1
 * Получает в качестве параметров элементы класса point: $start - пункт отправления, $finish - пункт прибытия, а также 
 * номера пунктов отправления и прибытия в графе $s и $f и сам граф $gr.
 * Возвращает путь в виде элемента класса path.
 */
function getMinChangesPath ($start, $finish, $s, $f, $gr) {
	$par = [];
	$ids = getStopIds ();
	$ids[] = $f;
	$ids[] = $s;
	foreach ($ids as $id) {
		$d[$id] = [(int) 1e9, (int) 1e9];
		$was[$id] = 0;
		$par[$id]["route"] = -2;
	}
	$d[$s] = [0, 0];
	for ($i = 0; $i < count ($ids); $i++) {
		$v = -1;
		foreach ($ids as $id) {
			if ($v == -1) $v = $id;
			if ($was[$id] == 0 && ($v == -1 || pairLess ($d[$id], $d[$v])))
				$v = $id;
		}
		$was[$v] = 1;
		if ($v == $f)
			break ;
		foreach ($gr[$v] as $stop) {
			if ($stop["route"] == $par[$v]["route"])
				continue;
			$cost = $stop["cost"];
			$to = $stop["to"];
			$nd = [$d[$v][0] + 1, $d[$v][1] + $cost];
			if (pairLess ($nd, $d[$to])) {
				$d[$to] = $nd;
				$par[$to] = array_merge (["v" => $v], $stop);
			}
		}
	}

	if ($d[$f][0] == (int) 1e9)
		return new path ();
	return getPathByPar ($par, $start, $finish, $s, $f); 
}

/*
 * Реализация алгоритма Дейкстры из пункта 5.3.2
 * Принимает в качестве параметров номера старта и финиша в графе $s и $f, сам граф $gr и текущее время $T в формате "HH:MM".
 * Возвращает массив ["d" => время, чтобы добраться от $s до $f, "par" => массив предков для восстановления пути].
 */ 
function Dijkstra ($s, $f, $gr, $T) {

	$par = [];
	$ids = getStopIds ();
	$ids[] = $f;
	$ids[] = $s;
	foreach ($ids as $id) {
		$d[$id] = 1e9;
		$was[$id] = 0;
		$par[$id]["route"] = -2;
	}
	$d[$s] = 0;
	for ($i = 0; $i < count ($ids); $i++) {
		$v = -1;
		foreach ($ids as $id) {
			if ($was[$id] == 0 && ($v == -1 || $d[$id] < $d[$v]))
				$v = $id;
		}
		$was[$v] = 1;
		if ($v == $f)
				break ;
		foreach ($gr[$v] as $stop) {
			if ($stop["route"] == $par[$v]["route"])
				continue;
			$cost = $stop["cost"];
			$to = $stop["to"];
			if ($stop["route"] != -1)
				$cost += getNextTime ($stop["to"], $stop["route"], timeToString (timeFromString ($T) + $d[$v]));
			if ($d[$to] > $d[$v] + $cost) {
				$d[$to] = $d[$v] + $cost;
				$par[$to] = array_merge (["v" => $v], $stop);
			}
		}
	}
	return ["d" => $d[$f], "par" => $par];
}

/*
 * Компаратор для сортировки путей в функции getFastPaths по возрастанию стоимости.
 */ 
function compPaths ($a, $b) {
	if ($a["d"] == $b["d"])
		return 0;
	return $a["d"] < $b["d"] ? -1 : 1;
}

/*
 * Делает расчет путей от точки $start до точки $finish по алгоритму из пункта 5.3.2
 * Получает в качестве параметров элементы класса point: $start - пункт отправления, $finish - пункт прибытия, а также 
 * номера пунктов отправления и прибытия в графе $s и $f и сам граф $gr.
 * Возвращает пути в виде массива, каждый элемент которого является элементом класса path.
*/
function getFastPaths ($start, $finish, $s, $f, $gr) {
	$T = getCurTime ();
	$best = Dijkstra ($s, $f, $gr, $T);
	if ($best["d"] == (int) 1e9)
		return [];
	$par = $best["par"];

	//echo "FAST PATHS\n";
	//print_r ($par);
	$order = [];
	for ($v = $par[$f]["v"]; $par[$v]["v"] != $s; $v = $par[$v]["v"]) {
		$order[] = $par[$v];
	}
	$paths = [];
	foreach ($order as $edge) {
		$cgr = $gr;
		for ($i = 0; $i < count ($gr[$edge["v"]]); $i++) {
			$tmp = ["to" => $edge["to"], "route" => $edge["route"], "dir" => $edge ["dir"], "cost" => $edge ["cost"]];
			if ($gr[$edge["v"]][$i] == $tmp) {
				unset ($cgr[$edge["v"]][$i]);
				break;
			}
		}
		$tmp = Dijkstra ($s, $f, $cgr, $T);
		if ($tmp["d"] != (int) 1e9) 
			$paths[] = $tmp;
	}
	
	$res = [getPathByPar ($par, $start, $finish, $s, $f)];
	usort ($paths, 'compPaths');
	if (count ($paths) > 0 && $paths[0]["d"] < (int) 1e9)
		$res[] = getPathByPar ($paths[0]["par"], $start, $finish, $s, $f);
	return $res;
}

/*
 * Компаратор для сортировки ребер по возрастанию ключа "cost" в списке смежности.
 */ 
function compEdges ($a, $b) {
	if ($a["cost"] == $b["cost"])
		return 0;
	return $a["cost"] < $b["cost"] ? -1 : 1;
}


/*
 * Делает расчет путей от точки $start до точки $finish (пункт 5.3)
 * Получает в качестве параметров элементы класса point: $start - пункт отправления, $finish - пункт прибытия.
 * Реализует часть с перестроением графа из алгоритма из пункта 5.3, граф хранится как список смежности.
 * Вызывает функции getFastPaths и getMinChangesPath
 * Если какие-то пути дублируются, удаляет повторы
 * Возвращает массив, каждый элемент которого - элемент класса path, задающий путь от $start до $finish.
 */
 
function getPaths ($start, $finish) {
	$gr = [];
	$ids = getStopIds ();
	foreach ($ids as $id)
		$gr[$id] = [];
	
	$fd = fopen ("Compute/graph", "r");
	fscanf ($fd, "%d", $m);
	for ($i = 0; $i < $m; $i++) {
		fscanf ($fd, "%d %d %d %d %d\n", $x, $y, $idRoute, $dir, $cost);
		$gr[$x][] = ["to" => $y, "route" => $idRoute, "dir" => $dir, "cost" => $cost];
	}
	fclose ($fd);
	$s = 10000;
	$f = 10001;
	$gr[$f] = [];
	
	$nearStart = getNearStops ($start, 3);
	$nearFinish = getNearStops ($finish, 5);
	
	//print_r ($nearStart);
	//print_r ($nearFinish);

	foreach ($nearStart as $stop) {
		$point = getObject ("point", $stop -> intPointId);
		$gr[$s][] = ["to" => $stop -> intStopId, "route" => -1, "dir" => 0, "cost" => round ($start -> dist ($point) * 15)];
	}
	foreach ($nearFinish as $stop) {
		$point = getObject ("point", $stop -> intPointId);
		$gr[$stop -> intStopId][] = ["to" => $f, "route" => -1, "dir" => 0, "cost" => round ($point -> dist ($finish) * 15)];
	}
	
	$ids[] = $s;
	$ids[] = $f;

	foreach ($ids as $id) {
		usort ($gr[$id], 'compEdges');
	}

	$res = getFastPaths ($start, $finish, $s, $f, $gr);
	$tmp = getMinChangesPath ($start, $finish, $s, $f, $gr);

	if (count ($tmp -> listPoints) > 0)
		$res[] = $tmp;
	$tmp = [];
	
	foreach ($res as $path) {
		$flag = false;
		foreach ($tmp as $pathTmp) {
			if ($pathTmp -> equal ($path))
				$flag = true;
		}
		if ($flag == false)
			$tmp[] = $path;
	}

	return $tmp;
}

?>

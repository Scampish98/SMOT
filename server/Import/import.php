<?php
	/*
	* Создаем временную папку для скачивания, разархивированния и обработки географических данных.
	* Помещаем в нее все файлы с кодами обработки.
	* Запускаем основной файл скачивания и обработки.
	* Удаляем временную папку.
	* Если загрузка была выполнена успешно, то в текущей директории появятся папка с изображиниями images и
	* файл с географическими данными result.data.
	* Иначе импортирование не удалось.
	*/

	mkdir ('temp');
	shell_exec ('cp -r cpp/* temp');
	shell_exec ('cp -r py/* temp');
	$ret = [];
	$parse = 0;
	chdir ('temp');
	exec ('python import.py', $ret, $parse);
	chdir ('..');
	shell_exec ('rm -r temp');

	if ($parse != 0) {
		echo ('Download error!');
		exit (1);
	}

	/*
	* Подключаемся к базе данных.
	* Очищаем её полностью.
	* Открываем файл с географическими данными для чтения.
	* Считываем количество интересующих нас точек и их координаты.
	* Добавляем их в БД.
	*/

	include_once ('../DB_recv/db.php');
	include_once ('../classes.php');

	clear ();
	$input = fopen ('result.data', 'r');
	$maxbuf = 100000;

	$points = (int) (trim (fgets ($input, $maxbuf)));

	$pointList = [];

	for ($i = 0; $i < $points; $i++) {
		$value = trim (fgets ($input, $maxbuf));
		$values = explode (" ", $value);
		$pt = new point ();
		$pt -> floatPointLat = (double) ($values[1]);
		$pt -> floatPointLon = (double) ($values[2]);
		
		$pt -> intPointId = insert ($pt);
		$pointList[$values[0]] = $pt;
	}

	/*
	* Открываем для чтения файл с данными о времени для маршрутов.
	* Для каждого маршрута добавим в БД информацию о его типе,
	* ссылку на картинку с отображением маршрута, всю информацию 
	* о временах (считаем из файла).
	* Файл с информацией о временах больше не требуется. Закроем его.
	*/

	$ids = [1, 2, 3, 4, 5, 6, 8];

	$routeList = [];

	$timeData = fopen ('troll_time.data', 'r');
	for ($i = 0; $i < 7; $i++) {
		$id = $ids[$i];
		$rt = new route ();
		
		$rt -> intRouteNum = $id;
		$rt -> strRouteType = 'Троллейбус';
		$rt -> strRouteMapUrl = 'http://www.testsite.loc/SMOT/images/route_' . (string) ($id) . '.jpg';
		
		$value = trim (fgets ($timeData, $maxbuf));
		$values = explode (" ", $value);
		$tm = explode (":", $values[0]);
		$rt -> timeRouteStartWeekOne = date ('H:i', mktime ((int) ($tm[0]), (int) ($tm[1])));
		$tm = explode (":", $values[1]);
		$rt -> timeRouteStartWeekTwo = date ('H:i', mktime ((int) ($tm[0]), (int) ($tm[1])));

		$value = trim (fgets ($timeData, $maxbuf));
		$values = explode (" ", $value);
		$tm = explode (":", $values[0]);
		$rt -> timeRouteStartSatOne = date ('H:i', mktime ((int) ($tm[0]), (int) ($tm[1])));
		$tm = explode (":", $values[1]);
		$rt -> timeRouteStartSatTwo = date ('H:i', mktime ((int) ($tm[0]), (int) ($tm[1])));
		
		$value = trim (fgets ($timeData, $maxbuf));
		$values = explode (" ", $value);
		$tm = explode (":", $values[0]);
		$rt -> timeRouteStartSunOne = date ('H:i', mktime ((int) ($tm[0]), (int) ($tm[1])));
		$tm = explode (":", $values[1]);
		$rt -> timeRouteStartSunTwo = date ('H:i', mktime ((int) ($tm[0]), (int) ($tm[1])));

		$value = trim (fgets ($timeData, $maxbuf));
		$values = explode (" ", $value);
		$tm = explode (":", $values[0]);
		$rt -> timeRouteEndWeekOne = date ('H:i', mktime ((int) ($tm[0]), (int) ($tm[1])));
		$tm = explode (":", $values[1]);
		$rt -> timeRouteEndWeekTwo = date ('H:i', mktime ((int) ($tm[0]), (int) ($tm[1])));

		$value = trim (fgets ($timeData, $maxbuf));
		$values = explode (" ", $value);
		$tm = explode (":", $values[0]);
		$rt -> timeRouteEndSatOne = date ('H:i', mktime ((int) ($tm[0]), (int) ($tm[1])));
		$tm = explode (":", $values[1]);
		$rt -> timeRouteEndSatTwo = date ('H:i', mktime ((int) ($tm[0]), (int) ($tm[1])));

		$value = trim (fgets ($timeData, $maxbuf));
		$values = explode (" ", $value);
		$tm = explode (":", $values[0]);
		$rt -> timeRouteEndSunOne = date ('H:i', mktime ((int) ($tm[0]), (int) ($tm[1])));
		$tm = explode (":", $values[1]);
		$rt -> timeRouteEndSunTwo = date ('H:i', mktime ((int) ($tm[0]), (int) ($tm[1])));

		$value = trim (fgets ($timeData, $maxbuf));
		$values = explode (" ", $value);
		$rt -> intRouteIntervalLeftWeek = (int) ($values[0]);
		$rt -> intRouteIntervalRightWeek = (int) ($values[1]);

		$value = trim (fgets ($timeData, $maxbuf));
		$values = explode (" ", $value);
		$rt -> intRouteIntervalLeftSat = (int) ($values[0]);
		$rt -> intRouteIntervalRightSat = (int) ($values[1]);

		$value = trim (fgets ($timeData, $maxbuf));
		$values = explode (" ", $value);
		$rt -> intRouteIntervalLeftSun = (int) ($values[0]);
		$rt -> intRouteIntervalRightSun = (int) ($values[1]);

		$rt -> intRouteId = insert ($rt);
		
		$routeList[$id] = $rt;		
	}
	fclose ($timeData);

	/*
	* Для каждолго маршрута считаем информацию о точках, через которе проходит этот маршрут.
	* Считаем информацию об остановках, через которые проходит этот маршрут.
	*/

	$paths = [];
	$lenPath = [];
	$stops = [];
	$lenStop = [];

	for ($i = 0; $i < 7; $i++) {
		$id = $ids[$i];
		$lenPath[$id] = [];
		$paths[$id] = [];
		$lenStop[$id] = [];
		$stops[$id] = [];
		for ($j = 0; $j < 2; $j++) {
			$lenPath[$id][$j] = (int) (trim (fgets ($input, $maxbuf)));
			$value = trim (fgets ($input, $maxbuf));
			$paths[$id][$j] = explode (" ", $value);
			$lenStop[$id][$j] = (int) (trim (fgets ($input, $maxbuf)));
			$value = trim (fgets ($input, $maxbuf));
			$stops[$id][$j] = explode (" ", $value);		
		}
	}

	/*
	* Считаем из файла список всех остановок с названиями.
	* Добавим в БД все связи остановок с точками (с указанием названий установок).
	* Файл с географическими данными больше не нужен. Закроем его.
	*/

	$stopList = [];
	$stp = (int) (trim (fgets ($input, $maxbuf)));

	for ($i = 0; $i < $stp; $i++) {
		$value = trim (fgets ($input, $maxbuf));
		$values = explode ("*", $value);
		$values[1] = str_replace ('"', '\'', htmlspecialchars_decode ($values[1]));
		$st = new stop ();
		$st -> strStopName = $values[1];
		$st -> intPointId = $pointList[$values[0]] -> intPointId;		
		
		$st -> intStopId = insert ($st);
		$stopList[$values[0]] = $st;
	}
	fclose ($input);

	/*
	* Добавим в БД все связи между остановками и маршрутами (с указанием номера маршрута, 
	* направления маршрута и порядковым номером остановки в маршруте).
	*/

	for ($i = 0; $i < 7; $i++) {
		$id = $ids[$i];
		for ($j = 0; $j < 2; $j++) {
			for ($k = 0; $k < $lenStop[$id][$j]; $k++) {
				$stR = new stopRoute ();
				
				$stR -> intStopId = $stopList[$stops[$id][$j][$k]] -> intStopId;
				$stR -> intRouteId = $routeList[$id] -> intRouteId;
				$stR -> intStopNum = $k;
				$stR -> boolDirection = $j;

				$stR -> intStopRouteId = insert ($stR);
			}
		}
	}

	/*
	* Добавим в БД все связи между точками и маршрутами (с указанием номера маршрута,
	* направления маршрута и порядковым номером точки в маршруте).
	*/


	for ($i = 0; $i < 7; $i++) {
		$id = $ids[$i];
		for ($j = 0; $j < 2; $j++) {
			for ($k = 0; $k < $lenPath[$id][$j]; $k++) {
				$ptR = new pointRoute ();
				
				$ptR -> intPointId = $pointList[$paths[$id][$j][$k]] -> intPointId;
				$ptR -> intRouteId = $routeList[$id] -> intRouteId;
				$ptR -> intPointNum = $k;
				$ptR -> boolDirection = $j;

				$ptR -> intPointRouteId = insert ($ptR);
			}
		}
	}
	require ("../Compute/buildGraph.php");
?>

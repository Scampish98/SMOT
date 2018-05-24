<?php
require_once ('../DB_recv/db.php');
/*
 * Вызывается после обновления БД
 * Перестраивает граф в файле graph в соответствии с текущими данными в БД
 * Реализует алгоритм из пункта 5.2
 */

 // массив, в котором будет содержаться список ребер.
$graph = [];
 // получаем все идентификаторы маршрутов
$routes = getRouteIds ();
foreach ($routes as $idRoute) {
  // выбираем направление, которое будет рассматривать
  for ($dir = 0; $dir <= 1; $dir++) {
    // получаем маршрут как последовательность остановок
    $route = getRouteAsStops ($idRoute, $dir);
    $d = [];
    // записываем в d[i] расстояние между i-1-ой и i-ой остановками
    for ($i = 1; $i < count ($route); $i++) {
      $part = getRouteBetweenStops ($route[$i - 1] -> intStopId, $route[$i]-> intStopId, $idRoute, $dir);
      $d[$i] = 0;
      for ($j = 1; $j < count ($part); $j++) {
	$d[$i] += $part[$j - 1] -> dist ($part[$j]);
      }
    }
    // заполняем граф
    for ($i = 1; $i < count ($route); $i++) {
      $s1 = $route[$i - 1];
      $dist = 0;
      for ($k = $i; $k < count ($route); $k++) {
	$s2 = $route[$k];
	$dist += $d[$k];
	$graph[] = [$s1 -> intStopId, $s2 -> intStopId, $idRoute, $dir, $dist * 3.0 / 2.0 ];
      }
    }
  }
}
// записываем в файл
$fd = fopen ("../Compute/graph", "w");
fprintf ($fd, "%d\n", count ($graph));
foreach ($graph as $a) {
  fprintf ($fd, "%d %d %d %d %d\n", $a[0], $a[1], $a[2], $a[3], ceil ($a[4]));
}
fclose ($fd);
?>

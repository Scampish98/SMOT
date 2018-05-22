<?php
require_once ('classes.php');
require_once ('DB_recv/db.php');
require_once ('Compute/compute.php');

function handlerPaths () {
  $p1 = new point (["floatPointLat" => $_POST["point1_lat"], "floatPointLon" => $_POST["point1_lon"]]);
  $p1 = new point (["floatPointLat" => $_POST["point2_lat"], "floatPointLon" => $_POST["point2_lon"]]);
  $paths = getPaths ($p1, $p2);
  return json_encode ($paths, JSON_UNESCAPED_UNICODE);
}

function handlerStopInfo () {
  $routes = getRoutesByStopId ($_POST["id"]);
  $res = [];
  foreach ($routes as $route) {
    $res[] = [$route -> strRouteType, $route -> intRouteNum, getNextTime ($_POST["id"], $route -> intRouteId, getCurTime ())];
  }
  return json_encode ($res, JSON_UNESCAPED_UNICODE);
}

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
  return json_encode ($res, JSON_UNESCAPED_UNICODE);
}

function handlerRoutes () {
  $routes = getRouteIds ();
  $res = [];
  foreach ($routes as $idRoute) {
    $route = getObject ("route", $idRoute);
    $res[$idRoute] = [$route -> strRouteType, $route -> intRouteNum];
  }
  return json_encode ($res, JSON_UNESCAPED_UNICODE);
}

function handlerStops () {
  $stops = getPointsByStops ();
  $res = [];
  foreach ($stops as $stop => $point) {
    $res[$stop] = [$point -> floatPointLat, $point -> floatPointLon];
  }
  return json_encode ($res, JSON_UNESCAPED_UNICODE);
}

switch ($_POST["name"]) {
  case "getPaths":
    echo handlerPaths ();
  case "getStopInfo":
    echo handlerStopInfo ();
  case "getRouteInfo":
    echo handlerRouteInfo ();
  case "getRoutes":
    echo handlerRoutes ();
  case "getStops":
    echo handlerStops ();
}
?>

<?php
	require_once  ('db.php');

	//clear ();
	//$p = new point (["floatPointLat" => 2.3213, "floatPointLon" => 312]);
	//$p = new stop (["strStopName" => "University", "intPointId" => 1]);
//	echo insert ($p) . "\n";
//	echo $p -> strStopName . "\n";
/*$id = insert (new stop (["strStopName" => "University", "intPointId" => 10]));
	$p = getObject ("point", 11);
	echo $p -> intPointId . " " . $p -> floatPointLat . "\n";
	$s = getObject ("stop", $id);
	echo $s -> intStopId . " " . $s -> strStopName . "\n";*/
//	print_r (getPointsByStops ());
	require_once ('compute.php');

//	buildGraph ();
/*	$s = new point (["floatPointLat" => 61.7957, "floatPointLon" => 34.3550]);
	$f = new point (["floatPointLat" => 61.7804, "floatPointLon" => 34.3844]);
	$res = getPaths ($s, $f);

	$tmp = json_encode ($res, JSON_UNESCAPED_UNICODE); 
	print_r ($tmp);
	print_r (json_decode ($tmp));*/
//	print_r (getNearStops ($p));

	clear ();

?>

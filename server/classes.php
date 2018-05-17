<?php
/*
 * Класс для хранения данных о точке в таком же формате, как в БД.
 */
class point {
	/*
	 * Порядок полей в БД.
	 */
	public static $order = ['intPointId', 'floatPointLat', 'floatPointLon'];

	public $intPointId;
	public $floatPointLat;
	public $floatPointLon;

	/*
	 * Конструктор для преобразования массива в объект данного класса.
	 */
	public function __construct (array $properties = array ()) {
		foreach ($properties as $key => $value) {
			$this -> {$key} = $value;
		}
	}

	/*
	 * Метод для перевода градусов в радианы.
	 */
	private function toRadian ($a) {
		return $a / 180.0 * acos (-1.0);
	}

	/*
	 * Метод для вычисления расстояния в километрах между двумя точками
	 * по заданным географическим координатам.
	 */
	public function dist ($p) {
		$phi1 = $this -> toRadian ($this -> floatPointLat);
		$phi2 = $this -> toRadian ($p -> floatPointLat);
		$l1 = $this -> toRadian ($this -> floatPointLon);
		$l2 = $this -> toRadian ($p -> floatPointLon);

		$d = acos (sin ($phi1) * sin ($phi2) + cos ($phi1) * cos ($phi2) * cos ($l1 - $l2));
		$R = 6371; // радиус Земли
		return $d * $R;
	}
}
/*
 * Класс для хранения данных об остановке в таком же формате, как в БД.
 */
class stop {
	/*
	 * Порядок полей в БД.
	 */
	public static $order = ['intStopId', 'strStopName', 'intPointId'];

	public $intStopId;
	public $strStopName;
	public $intPointId;

	/*
	 * Конструктор для преобразования массива в объект данного класса.
	 */
	public function __construct (array $properties = array ()) {
		foreach ($properties as $key => $value) {
			$this -> {$key} = $value;
		}
	}
}

/*
 * Класс для хранения данных о маршруте в таком же формате, как в БД.
 */
class route {
	/*
	 * Порядок полей в БД.
	 */
	public static $order = [ 'intRouteId', 'intRouteNum', 'strRouteType',
													'strRouteMapUrl', 'timeRouteStartWeekOne', 'timeRouteEndWeekOne',
													'timeRouteStartWeekTwo', 'timeRouteEndWeekTwo', 'timeRouteStartSatOne',
													'timeRouteEndSatOne', 'timeRouteStartSatTwo', 'timeRouteEndSatTwo',
													'timeRouteStartSunOne', 'timeRouteEndSunOne', 'timeRouteStartSunTwo',
													'timeRouteEndSunTwo', 'intRouteIntervalLeftWeek', 'intRouteIntervalRightWeek',
													'intRouteIntervalLeftSat', 'intRouteIntervalRightSat',
													'intRouteIntervalLeftSun', 'intRouteIntervalRightSun'];

	public $intRouteId;
	public $intRouteNum;
	public $strRouteType;
	public $strRouteMapUrl;
	public $timeRouteStartWeekOne;
	public $timeRouteEndWeekOne;
	public $timeRouteStartWeekTwo;
	public $timeRouteEndWeekTwo;
	public $timeRouteStartSatOne;
	public $timeRouteEndSatOne;
	public $timeRouteStartSatTwo;
	public $timeRouteEndSatTwo;
	public $timeRouteStartSunOne;
	public $timeRouteEndSunOne;
	public $timeRouteStartSunTwo;
	public $timeRouteEndSunTwo;
	public $intRouteIntervalLeftWeek;
	public $intRouteIntervalRightWeek;
	public $intRouteIntervalLeftSat;
	public $intRouteIntervalRightSat;
	public $intRouteIntervalLeftSun;
	public $intRouteIntervalRightSun;

	/*
	 * Конструктор для преобразования массива в объект данного класса.
	 */
	public function __construct (array $properties = array ()) {
		foreach ($properties as $key => $value) {
			$this -> {$key} = $value;
		}
	}
}

/*
 * Класс для хранения данных о связи маршрута с точкой в таком же формате, как в БД.
 */
class pointRoute {
	/*
	 * Порядок полей в БД.
	 */
	public static $order = ['intPointRouteId', 'intPointId', 'intRouteId',
													'intPointNum', 'boolDirection'];

	public $intPointRouteId;
	public $intPointId;
	public $intRouteId;
	public $intPointNum;
	public $boolDirection;

	/*
	 * Конструктор для преобразования массива в объект данного класса.
	 */
	public function __construct (array $properties = array ()) {
		foreach ($properties as $key => $value) {
			$this -> {$key} = $value;
		}
	}
}

/*
 * Класс для хранения данных о связи маршрута с остановкой в таком же формате, как в БД.
 */
class stopRoute {
	/*
	 * Порядок полей в БД.
	 */
	public static $order = ['intStopRouteId', 'intStopId', 'intRouteId',
													'intStopNum', 'boolDirection'];

	public $intStopRouteId;
	public $intStopId;
	public $intRouteId;
	public $intStopNum;
	public $boolDirection;

	/*
	 * Конструктор для преобразования массива в объект данного класса.
	 */
	public function __construct (array $properties = array ()) {
		foreach ($properties as $key => $value) {
			$this -> {$key} = $value;
		}
	}
}

/*
 * Класс для хранения пути.
 * Свойство $listPoints хранит последовательность точек, образующую путь, в виде массива.
 * Свойство $listRoutes хранит последовательность пересадок в виде массива, каждый элемент которого
 * является массивом из четырех элементов:
 * ['тип транспорта', 'номер транспорта', 'название остановки посадки', 'название остановки высадки']
 */
class path {
	public $listPoints;
	public $listRoutes;

	/*
	 * Метод для сравнения двух путей на равенство. Сравнивается по массиву $listRoutes.
	 * Принимает в качестве параметра элемент класса path: $p - путь, с которым требуется провести сравнение.
	 */
	function equal ($p) {
		return $this -> listRoutes == $p -> listRoutes;
	}
}

/*
 * Класс для хранения только координат для точек (для удобства преобразования в json)
 */ 
class __point {
	/*
	 * Долгота и широта соответственно.
	 */ 
	public $latitude;
	public $longitude;
	
	/*
	 * Конструктор для преобразования объекта класса point в объект класса __point.
	 */
	public function __construct ($point) {
		$this -> latitude = $point -> floatPointLat;
		$this -> longitude = $point -> floatPointLon;
	}
}	

?>

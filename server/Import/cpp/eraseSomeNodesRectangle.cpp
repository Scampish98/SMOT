#include "fastInput.h"
#include "xml.h"

// string mnLon, mxLon, mnLat, mxLat минимальное и максимальное значения для широт и долгот

string mnLon, mxLon, mnLat, mxLat;

/*
* Вспомогательная функция для приведения вещественных чисел к единому формату
* 5 символов до вещественной точки и 15 символов после
*
* string t вещественное число в строковом представлении
*
* Разбивает число на часть до точки и после. Дополняет их до соответствующих длин нулями и соединяет обратно
*/

string sToD (string t) {
	string a, b;
	for (auto it : t) {
		if (it == '.') {
			a.swap (b);
			continue;
		}
		b += it;
	}
	while (a.size () < 5u) a = "0" + a;
	while (b.size () < 10u) b += '0';
	return a + b;
}

/*
* Функция для считывания координат точек контура Петрозаводска из файла PtzBounds.txt
* и получения границ минимального прямоугольника, содержащего все заданные точки
* 
* Считывает из файла количество точек, а потом координаты точек. По каждой координате находятся
* минимальное и максимальное значения. Это и будут границы прямоугольника
*/

void load () {
	mnLat = mnLon = string (15, '9');
	mxLat = mxLon = string (15, '0');
	string lat, lon;
	ifstream in;
	in.open ("PtzBounds.txt", std::ifstream::in);

	int n;
	in >> n;

	for (int i = 0; i < n; i++) {
		in >> lat >> lon;
		lat = sToD (lat);
		lon = sToD (lon);
		mnLat = min (mnLat, lat);
		mxLat = max (mxLat, lat);
		mnLon = min (mnLon, lon);
		mxLon = max (mxLon, lon);
	}

	in.close ();
}

// bufferedReader input поток ввода

bufferedReader input;

int main () {
	double st = clock ();
		
	// Считываем координаты точек из файла и получаем границы прямоугольника

	load ();
	
	// Открываем для считывания файл RU-KR.osm
	
	input.init ("RU-KR.osm");

	// Открываем для вывода файл myFile.osm

	assert (freopen ("myFile.osm", "w", stdout));

	// tag temp элемент для хранения считанного тега

	tag temp;

	// До тех пор пока не считаны все теги со ввода читаем теги и обрабатываем их
	// Если это тег точки, не попадающей в найденный прямоугольник, игнорируем её 
	// Остальные теги выводим в myFile.osm

	while (!input.checkEof ()) {
		temp.initTag (input);
		if (temp.name == "node") {            
			string lat = sToD (temp.attr["lat"]);
			string lon = sToD (temp.attr["lon"]);

			if (lat < mnLat || lat > mxLat) continue;
			if (lon < mnLon || lon > mxLon) continue;
		}
		if (!temp.checkEmpty ()) puts (temp.toXml ().c_str ());
	}

	// Закрываем поток ввода

	input.close ();

	clog << (clock () - st) / CLOCKS_PER_SEC << endl;

	return 0;
}

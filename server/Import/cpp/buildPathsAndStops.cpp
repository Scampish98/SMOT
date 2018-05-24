#include "fastInput.h"
#include "xml.h"

typedef long double dbl;

/*
* const dbl eps допустимая погрешность при сравнении
* const dbl pi константа PI
*/

const dbl eps = 1e-9;
const dbl pi = acosl (-1.0);

/*
* Функция сравнения двух вещественных чисел
* 
* dbl a, dbl b сравниваемые числа
*
* Считаем числа равными, если они отличаются не более чем на eps
*/

inline bool doubleEqual (dbl a, dbl b) {
	return abs (a - b) < eps;
}

/*
* Структура для хранения географической точки
*/

struct point {
	/*
	* dbl lat, lon ширина и долгота точки 
	*/

	dbl lat, lon;

	/*
	* Конструктор по-умолчанию
	*/

	point () {}

	/*
	* Конструктор по широте и долготе
	*/

	point (dbl lat, dbl lon) : lat (lat), lon (lon) {}

	/*
	* Метод перевода градусов в радианы
	*
	* dbl a угол в градусах, который надо перевести в радианы
	*
	* Переводим в радианы по формуле
	*/

	dbl toRadian (dbl a) {
		return a * pi / 180;
	}

	/*
	* Метод получения географического расстояния между двумя точками
	*
	* point p точка, до которой считаем расстояние
	*
	* Считаем расстояние по формуле
	*/

	dbl geoDist (point p) {
		dbl phi1 = toRadian (lat);
		dbl phi2 = toRadian (p.lat);
		dbl l1 = toRadian (lon);
		dbl l2 = toRadian (p.lon);
		dbl d = acos (sin (phi1) * sin (phi2) + cos (phi1) * cos(phi2) * cos (l1 - l2));
		return d * 6371;
	}
	
	/*
	* Оператор сравнения двух точек
	*
	* const point &p точка, с которой производится сравнение
	*
	* Говорим, что точки совпадают, если их координаты совпадают
	*/	

	bool operator == (const point &p) const {
		return doubleEqual (lat, p.lat) && doubleEqual (lon, p.lon);
	}
};

/*
* bufferedReader input поток для быстрого считывания
* unordered_map<string, tag> node, way, relation ассоциативные массивы для хранения тегов точек, путей, отношений по их id
* unordered_set<string> stops список id для остановок
* vector<string> pth[9][2], stp[9][2] списки точек и остановок для всех маршрутов
* int cnt[9] вспомогательный массив для получения количества направлений для каждого маршрута
*/

bufferedReader input;

unordered_map<string, tag> node, way, relation;
unordered_set<string> stops;

vector<string> pth[9][2], stp[9][2];
int cnt[9];

/*
* Функция для считывания обрабатываемой информации
* 
* Открываем для считывания файл RU-KR.osm. До тех пор, пока в нем есть теги, считываем их.
* Считаем, что есть теги всего трех типов: node, way, relation. Запоминаем их в соответствующие списки.
* Закрываем поток ввода.
*/

void load () {
	input.init ("RU-KR.osm");
	tag temp;

	while (!input.checkEof ()) {
		temp.initTag (input);
		string id = temp.attr["id"];
		if (temp.name == "node") {
			node[id] = temp;
		}
		else if (temp.name == "way") {
			way[id] = temp;
		}
		else {
			relation[id] = temp;
		}
	}

	input.close ();
}

/*
* Функция для получения тега, на который ссылается текущий тег
*
* tag T тег, ссылающийся на искомый
*
* Получаем у текущего тега атрибут ссылки ref (это id искомого тега). 
* Получаем тип тега, на который ведет ссылка. Получаем нужный тег из соответствующего списка
*/

inline tag getTag (tag T) {
	string id = T.attr["ref"];
	string type = T.attr["type"];

	if (type == "node") return node[id];
	else if (type == "way") return way[id];
	else return relation[id];
}

/*
* point last вспомогательная точка. Хранит последнюю точку строящегося маршрута
* unordered_set<string> was список id точек, которые последними были добавлены в маршрут
*/

point last;
unordered_set<string> was;

/*
* Функция для получения географической точки по id
*
* сonst string &id id искомой точки
*
* Получаем тег из нужного списка. Получаем его атрибуты широты и долготы и генерируем point
*/

inline point getPoint (const string &id) {
	auto nd = node[id];
	return point (atof (nd.attr["lat"].c_str ()), atof (nd.attr["lon"].c_str ()));
}

/*
* Функция для добавления отношения way в список точек маршрута
*
* tag T тег отношения way, хранящий список точек, vector<string> &ids список уже добавленных в маршрут точек
*/

inline void addWay (tag T, vector<string> &ids) {
	// Если маршрут пуст, то надо зачистить was (там мусор с прошлого маршрута).

	if (ids.empty ()) was.clear ();

	// Получаем список вложенных тегов по имени nd. По списку тегов получаем список точек. 

	auto list = T.findSubtagByName ("nd");
	vector<string> I;
	for (auto it : list) {
		I.push_back (it.attr["ref"]);
	}

	//Список точек дан нам либо в прямом, либо в обратном порядке. Смотрим, какой из концов пути ближе к
	// последней точке построенного маршрута. Этот конец будет началом.

	point p1 = getPoint (I.front ());
	point p2 = getPoint (I.back ());
	if (last.geoDist (p1) > last.geoDist (p2)) reverse (I.begin (), I.end ());

	// Добавляемый путь мог частично пересекаться с уже имеющимися точками.
	// Если пересечение есть, не будем добавлять его дважды.

	auto cp = I;
	I.clear ();
	for (auto it : cp) {
		if (was.count (it)) I.clear ();
		else I.push_back (it);
	}

	// Избавившись от пересечений добавим все оставшиеся точки в список точек пути.

	was.clear ();
	for (auto it : I) { 
		was.insert (it);
		ids.push_back (it);
	}
	last = getPoint (ids.back ());
}

/*
* Функция для получения позиции точки в маршруте, которая является установкой с указанным id. 
*
* vector<string> &list список точек в маршруте, const string &pointId id остановки, которую мы хотим найти
*
* Пробегаем по списку. Если нашли нужный id, то вернем позицию. Иначе вернем число, которое не может быть корректной позицией
*/

inline int stupidInsertPoint (vector<string> &list, const string &pointId) {
	int n = (int) list.size ();
	for (int i = 0; i < n; i++) {
		if (list[i] == pointId) return i;
	}
	return n;
}

/*
* Функция для вставки новой точки в маршрут 
*
* vector<string> &list список имеющихся точек в маршруте, const string &pointId точка, которую хотим вставить, 
* int l позиция в маршруте, левее которой нельзя вставить новую точку,
* int r позиция в маршруте, правее которой нельзя вставить новую точку,
*
* Пробегаем по списку между указанными границами и найдем позицию, в которую оптимально вставить новую точку.
* Вставим её туда
*/ 

inline int insertPoint (vector<string> &list, const string &pointId, int l, int r) {
	int pos = l;
	dbl mnDst = 1e9;
	auto p = getPoint (pointId);
	for (int i = l + 1; i <= r; i++) {
		auto p1 = getPoint (list[i - 1]);
		auto p2 = getPoint (list[i]);
		dbl v1 = p.geoDist (p1);
		dbl v2 = p.geoDist (p2);
		if (v1 + v2 < mnDst) {
			mnDst = v1 + v2;
			pos = i;
		}
	}
	list.insert (list.begin () + pos, pointId);
	return pos;
}

/*
* Функция для вставки в маршрут списка остановок
*
* vector<string> &pth список точек маршрута, vector<string> &stp список остановок, которые мы хотим вставить
*
* Пробегаем по списку остановок. Для остановки получаем границы позиций, на которых она может оказаться.
* Вставляем точку при помощи insertPoint
*/

inline void insertStops (vector<string> &pth, vector<string> &stp) {
	int n = (int) stp.size ();

	for (int i = 0, l = 0; i < n; i++) {
		int r = (int) pth.size () - 1;
		for (int j = i + 1; j < n; j++) {
			r = min (r, stupidInsertPoint (pth, stp[j]));
		}
		l = insertPoint (pth, stp[i], l, r);
	}
}

/*
* Функция получения номера маршрута из тега
*
* tag T тег, хранящий в себе описание маршрута
*
* Получаем список вложенных тегов с именем tag, среди них находим тег, хранящий номер маршрута. 
* Получаем из него номер маршрута
*/

int getF (tag T) {
	auto list = T.findSubtagByName ("tag");
	for (auto it : list) {
		if (it.attr["k"] == "ref") return atoi (it.attr["v"].c_str ());
	}
	return -1;
}

/*
* Добавление из тега relation всех тегов, на которые он ссылается
*
* tag T тег, из которого добавляем ссылки
*/

inline void addRelation (tag T) {
	
	// Получаем список вложенных тегов с именем member

	auto list = T.findSubtagByName ("member");

	// Получаем номер маршрута, за который отвечает тег
	// Если тег не отвечает за маршрут, то зависимости нас не интересуют
	
	int f = getF (T);
	if (f < 0) return;
	
	// Получаем направление указанного маршрута

	int s = cnt[f]++;

	// Получаем список отсановок указанного маршрута
	
	for (auto it : list) {
		it = getTag (it);
		string id = it.attr["id"];
		if (it.name == "node") {
			stp[f][s].push_back (id);
			stops.insert (id);
		}
	}

	// Говорим, что маршрут стартует в первой остановке

	last = getPoint (stp[f][s].front ());

	// Получаем пути, составляющие маршрут, и добавляем их в маршрут

	for (auto it : list) {
		it = getTag (it);
		string id = it.attr["id"];
		if (it.name == "way") {
			addWay (it, pth[f][s]);		
		}
	}

	// Вставляем в маршрут остановки

	insertStops (pth[f][s], stp[f][s]);
}

int main () {
	double st = clock ();

	// Загружем теги

	load ();

	// Открываем файл для вывода информации в удобном для пользователя формате

	assert (freopen ("myFile.osm", "w", stdout));

	// Получаем списки точек и остановок для всех маршрутов

	for (auto it : relation) {
		addRelation (it.second);
	}

	// Для всех точек. которые не являются остановками, удаляем все вложенные теги
	// Для точек, которые являются остановками, оставляем вложенный тег с названием остановки
	// Остальные вложенные теги удаляем

	for (auto &it : node) {
		string id = it.first;
		if (stops.count (id)) {
			auto list = it.second.findSubtagByName ("tag");
			it.second.eraseSubTagsExceptNames ( { } );
			for (auto jt : list) {
				if (jt.attr["k"] == "name") {
					it.second.insideTags.push_back (jt);
					break;
				}
			}
		}
		else {
			it.second.eraseSubTagsExceptNames ( { } );
		}
	}
	
	// Выводим количество точек на карте.

	cout << node.size () << endl;
	
	// Выводим географические координаты точек
	
	for (auto it : node) {
		tag temp = it.second;
		string res = temp.attr["id"] + " " + temp.attr["lat"] + " " + temp.attr["lon"];
		puts (res.c_str ());
	}
	
	// Для каждого маршрута выводим количество точек в нем, список id этих точек в поряде следования
	// Количество остановок в маршруте и их id в порядке следования

	for (int i = 1; i <= 8; i++) {
		if (i == 7) continue;
		string res;
		for (int j = 0; j < 2; j++) {
			res += to_string (pth[i][j].size ()) + "\n";
			for (auto it : pth[i][j]) res += it + " ";
			res += "\n";
			res += to_string (stp[i][j].size ()) + "\n";
			for (auto it : stp[i][j]) res += it + " ";
			puts (res.c_str ());
			res.clear ();
		}
	}
	
	// Выводим количество остановок на карте

	printf ("%d\n", (int) stops.size ());
	
	// Выводим список id остановок

	for (auto it : stops) {
		string res = it;
		auto t = node[it];
		t = t.findSubtagByName ("tag") [0];
		res += "*" + t.attr["v"];
		puts (res.c_str ());
	}

	clog << (clock () - st) / CLOCKS_PER_SEC << endl;

	return 0;
}

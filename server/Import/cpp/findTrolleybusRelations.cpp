#include "fastInput.h"
#include "xml.h"

/*
* bufferedReader input поток для быстрого считывания
* unordered_map<string, tag> node, way, relation ассоциативные массивы для хранения тегов точек, путей, отношений по их id
* unordered_map<string, tag> resultNode, resultWay, resultRelation ассоциативные массивы 
* для хранения необходимых для маршрутов тегов точек, путей, отношений по их id
* unordered_set<string> wasRelation, wasWay 
*/

bufferedReader input;

unordered_map<string, tag> node, way, relation, resultNode, resultWay, resultRelation;
unordered_set<string> wasRelation, wasWay;

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
* Функция для добавления тега в список необходимых
* 
* tag T добавляемый тег
*
* Определяем тип тега и добавляем его в соответсвующий список
*/

inline void addTag (tag T) {
	string id = T.attr["id"];
	if (T.name == "node") {
		resultNode[id] = T;
	}
	else if (T.name == "way") {
		resultWay[id] = T;
	}
	else if (T.name == "relation") {
		resultRelation[id] = T;
	}
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
* Функция для добавления точек маршрута в список необходимых из тега типа "way"
*
* tag T тег отношения way, хранящий список точек
*
* Получаем список точек, на которые ссылается тег. Добавляем все найденные точки в список необходимых
*/

void addWay (tag T) {
	string id = T.attr["id"];
	if (wasWay.count (id)) return;
	wasWay.insert (id);
	auto list = T.findSubtagByName ("nd");
	for (auto it : list) {
		string nodeId = it.attr["ref"];
		addTag (node[nodeId]);
	}
}

/*
* Добавление из тега relation всех тегов, на которые он ссылается
*
* tag T тег, из которого добавляем ссылки
* 
* Получаем список вложенных тегов с именем member
* Добавляем все теги из этого списка
*/

inline void addRelation (tag T) {
	string id = T.attr["id"];
	if (wasRelation.count (id)) return;
	wasRelation.insert (id);

	auto list = T.findSubtagByName ("member");

	for (auto it : list) {
		auto temp = getTag (it);
		addTag (temp);
		if (temp.name == "relation") {
			addRelation (temp);
		}
		else if (temp.name == "way") {
			addWay (temp);
		}
	}
}

int main () {
	double st = clock ();
	
	// Загружем теги

	load ();

	// Открываем файл для вывода информации в удобном для пользователя формате

	input.init ("RU-KR.osm");
	
	// Открываем файл для вывода информации в удобном для пользователя формате

	assert (freopen ("myFile.osm", "w", stdout));
	
	// tag temp элемент для хранения считанного тега

	tag temp;

	// До тех пор пока не считаны все теги со ввода читаем теги и обрабатываем их
	// Если это не тег relation, то игнорируем его 
	// Иначе смотрим является ли это отношение троллейбусным маршрутом
	// Если да, то добавляем его в список необходимых

	while (!input.checkEof ()) {
		temp.initTag (input);
		if (temp.name != "relation") continue;
		auto nodes = temp.findSubtagByName ("tag");

		int good = 0;
		
		for (auto it : nodes) {
			if (!it.attr.count ("k") || !it.attr.count ("v")) continue;
			if (it.attr["k"] != "route" || it.attr["v"] != "trolleybus") continue;			
			good++;
			break;
		}

		for (auto it : nodes) {
			if (!it.attr.count ("k") || !it.attr.count ("v")) continue;
			if (it.attr["k"] != "type" || it.attr["v"] != "route_master") continue;
			good++;
			break;
		}

		if (2 != good) continue;

		addRelation (temp);
	}

	// Закрываем поток ввода

	input.close ();

	// Выводим все необходимые теги

	for (auto t : resultNode) {
		puts (t.second.toXml ().c_str ());
	}
	for (auto t : resultWay) {
		puts (t.second.toXml ().c_str ());
	}
	for (auto t : resultRelation) {
		puts (t.second.toXml ().c_str ());
	}

	clog << (clock () - st) / CLOCKS_PER_SEC << endl;

	return 0;
}

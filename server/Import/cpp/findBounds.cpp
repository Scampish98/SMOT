#include "fastInput.h"
#include "xml.h"

// vector<string> nodesID списко id точек контура Петрозаводска
// vector<string> lat, lon долготы и широты точек из контура
// unordered_map<string, vector<int> > pos таблица соответствия между id точек и их географическими координатами

vector<string> nodesID;
vector<string> lat, lon;
unordered_map<string, vector<int> > pos;

/*
* Функция для считывания id точек контура Петрозаводска из файла PtzID.txt
* 
* Считывает из файла количество точек, а потом и сами точки
*/

void load () {
	string temp;
	ifstream in;
	in.open("PtzID.txt", std::ifstream::in);

	int n;
	in >> n;

	for (int i = 0; i < n; i++) {
		in >> temp;
		nodesID.push_back (temp);
		pos[temp].push_back (i); 
	}

	lat.resize (nodesID.size ());
	lon = lat;
                
	in.close ();
}

// bufferedReader input поток ввода

bufferedReader input;

int main () {
	double st = clock ();

	// Считываем id точек из файла

	load ();

	// Открываем для считывания файл RU-KR.osm
	
	input.init ("RU-KR.osm");
	
	// Открываем для вывода файл PtzBounds.txt
	// В нем будет содержаться список географических координат точек, 
	// которые образуют контур Петрозаводска

	assert (freopen ("PtzBounds.txt", "w", stdout));
	
	// tag temp элемент для хранения считанного тега

	tag temp;

	// До тех пор пока не считаны все теги со ввода читаем теги и обрабатываем их
	// Если это тег точки, с таким id, который содержится в контуре Петрозаводска, 
	// то получаем и сохраняем географические координаты этой точки

	while (!input.checkEof ()) {
		temp.initTag (input);
		if (temp.name != "node") continue;
		auto id = temp.attr["id"];
		if (!pos.count (id)) continue;
		for (auto i : pos[id]) {
			lat[i] = temp.attr["lat"];
			lon[i] = temp.attr["lon"];
		}
	}

	// Закрываем поток ввода

	input.close ();

	// Выводим количество точек в контуре

	cout << nodesID.size () << '\n';

	// Выводим координаты точек контура

	for (int i = 0; i < (int) nodesID.size (); i++) {
		cout << lat[i] << ' ' << lon[i] << '\n'; 
	}

	clog << (clock () - st) / CLOCKS_PER_SEC << endl;

	return 0;
}

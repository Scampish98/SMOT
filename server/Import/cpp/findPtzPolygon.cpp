#include "fastInput.h"
#include "xml.h"

// const string wayID id пути, хранящего контур Петрозаводска

const string wayID = "60388877";

// bufferedReader input поток ввода

bufferedReader input;

int main () {
	double st = clock ();
	
	// Открываем для считывания файл RU-KR.osm
	
	input.init ("RU-KR.osm");
	
	// Открываем для вывода файл PtzID.txt
	// В нем будет содержаться список точек, которые образуют контур Петрозаводска
	
	assert (freopen ("PtzID.txt", "w", stdout));

	// vector<string> ans список для хранения искомых точек
	// tag temp элемент для хранения считанного тега

	vector<string> ans;
	tag temp;
	
	// До тех пор пока не считаны все теги со ввода читаем теги и обрабатываем их
	// Если это тег пути, с искомым id, то получаем из него список точек контура и завершаем цикл

	while (!input.checkEof ()) {
		temp.initTag (input);
		if (temp.name != "way") continue;
		if (temp.attr["id"] != wayID) continue;
		auto res = temp.findSubtagByName ("nd");
		for (auto it : res) ans.push_back (it.attr["ref"]);
        	break;
	}
	
	// Закрываем поток ввода

	input.close ();
	
	// Выводим количество точек в контуре

	cout << ans.size () << endl;
	
	// Выводим id этих точекыыы
	
	for (auto it : ans) cout << it << endl;

	clog << (clock () - st) / CLOCKS_PER_SEC << endl;

	return 0;
}

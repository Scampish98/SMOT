#include "fastInput.h"
#include "xml.h"

// bufferedReader input поток ввода

bufferedReader input;

int main () {
	double st = clock ();
	
	// Открываем для считывания файл RU-KR.osm

	input.init ("RU-KR.osm");

	// Открываем для вывода файл myFile.osm

	assert (freopen ("myFile.osm", "w", stdout));

	// Пропускаем лишнюю информацию

	{
		string test;          
		readTag (input, test);
		readTag (input, test);
	}

	// tag temp элемент для хранения считанного тега

	tag temp;
	
	// До тех пор пока не считаны все теги со ввода читаем теги и обрабатываем их
	// Выкидываем теги, которые никак не связаны с географическими объектами
	// В оставшихся тегах удаляем бесполезные атрибутыы

	while (!input.checkEof ()) {
		temp.initTag (input);
		if (temp.checkEmpty ()) break;	
		if (temp.name == "bounds") continue;
		temp.deleteAttr ("user");
		temp.deleteAttr ("uid");      
		temp.deleteAttr ("timestamp");
		temp.deleteAttr ("changeset");
		temp.deleteAttr ("version");
		puts (temp.toXml ().c_str ());
	}
	
	// Закрываем поток ввода

	input.close ();

	clog << (clock () - st) / CLOCKS_PER_SEC << endl;

	return 0;
}

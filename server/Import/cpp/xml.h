#ifndef XML
#define XML

#include <bits/stdc++.h>
#include "fastInput.h"

using namespace std;

// const string someRandomeNameForNullName имя, которое будет у пустых тегов.
const string nullName = "someRandomeNameForNullName";

/*
* Функция, которая считывает из указанного источника ввода последовательность, ограниченную символами '<' и '>'
* bufferedReader &input источник ввода
* string &t строка, в которую сохраняем результат
*
* Читаем из ввода символы до тех пор, пока не встретим '<' или EOF
* Если встретили EOF, то завершаем работу функции
* Иначе считываем и сохраняем сиволы до тех пор, пока не встретим '>'
*/

void readTag (bufferedReader &input, string &t) {
	t.clear ();
	while (!input.checkEof () && input.curChar () != '<') input.getChar ();
	if (input.checkEof ()) return;
	input.getChar ();
	while (input.curChar () != '>') t += input.getChar ();
	input.getChar ();
}

/*
* Функция разбирает тег на имя и атрибуты.
*
* string &name будет хранить имя тега
* map<string, string> &attr будет хранить список атрибутов тега в виде пары ключ-значение
* string t текст, который надо разбить
*
* Читаем имя тега
* Далее, пока не дойдем до конца строки, пытаемся получать пары вида ключ-значение
*/

void splitTag (string &name, map<string, string> &attr, string t) {
	name.clear ();
	attr.clear ();
	
	auto it = t.begin ();
	
	while (isspace (*it)) it++;
	
	while (it != t.end () && !isspace (*it)) {
		name += *it;
		it++;
	}

	while (0==0) {
		while (it != t.end () && isspace (*it)) it++;
		if (it == t.end () || *it == '/') break;
		string a;
		while (*it != '=') {
			a += *it;
			it++;
		}
		it++;
		it++;
		string b;
		while (*it != '"') {
			b += *it;
			it++;
		}
		it++;
		attr[a] = b;
	}             
}

/*
* Структура для хранения тега формата osm
*/

struct tag {
	/*    
	* string name имя тега
	* map<string, string> attr список атрибутов в формате ключ-значение
	* vector<tag> insideTags список вложенных тегов
	*/

	string name;
    	map<string, string> attr;
	vector<tag> insideTags;
	
	/*
	* Конструктор по-умолчанию
	*/

	tag () {
		name = nullName;
	}

	/*
	* Метод, находящий вложеные теги с указанным именем
	* Возвращает список тегов
	*
	* const string &name имя, по которому проводится поиск вложенных тегов
	*
	* Проходим по списку вложенных тегов и копируем подходящие по имени в отдельный список
	* Возвращаем этот список как результат
	*/

	vector<tag> findSubtagByName (const string &name) {
		vector<tag> res;
		for (auto it : insideTags) {
			if (it.name == name) res.push_back (it);
		}
		return res;
	}

	/*
	* Метод, считывающий тег из потока ввода
	*
	* bufferedReader &input поток ввода, string outName имя внешнего тега, если имеется
	*
	* Рекурсивно считываем тег. Если тег не разделен на открывающий и закрывающий, то выходим из метода после 
	* считывания. Иначе уходим в рекурсию до тех пор, пока не встретим закрывающий тег
	*/

	void initTag (bufferedReader &input, string outName = "NULL") {
		string tg;
		name = nullName;
		attr.clear ();      
		insideTags.clear ();
		readTag (input, tg);
		if (tg.empty ()) return;

		if (tg == "/" + outName) {
			name = nullName;
			return;
		}

		splitTag (name, attr, tg);


		if (name == "/osm") {
			name = nullName;
			return;	
		}

		if (tg.back () == '/') return;
		while (0==0) {
			tag nxt;
			nxt.initTag (input, name);
			if (nxt.checkEmpty ()) break;
			insideTags.push_back (nxt);
		}
	}

	/*
	* Метод, удаляющий из тега атрибут с указанным именем
	*
	* const string &t bимя удаляемого атрибута
	*
	* Проверяем наличие у тега атрибута с заданным именем. При наличии -- удаляем 
	*/

	void deleteAttr (const string &t) {
		auto it = attr.find (t);
		if (it == attr.end ()) return;
		attr.erase (it);
	}

	/*
	* Метод, приводящий тег в формат xml. Возвращает его в виде строки
	*
	* string add добавление табуляции для вложенных тегов
	*
	* Генерируем строку для текущего тега в виде имени и артибутов. Если нет вложенных тегов, то не делаем 
	* разделения на открывающий и закрывающий и завершаемся. Иначе говорим, что текущий тег открывающий и уходим в рекурсию
	*/

	string toXml (string add = "") {
    		string res = add + "<" + name;
    		for (auto it : attr) {
    			res += " " + it.first + "=\"" + it.second + "\"";
    		}
    		if (insideTags.empty ()) {
    			res += "/>";
    		}			
    		else {
    			res += ">";     		
    			for (auto it : insideTags) {
    				res += "\n" + it.toXml (add + '\t');
    			}
    			res += "\n</" + name + ">";
    		}
    		return res;			
	}

	/*
	* Метод, проверяющий, что текущий тег пустой
	*
	* Проверяем, что имя тега совпадает с именем пустых тегов
	*/

	inline bool checkEmpty () const {
		return name == nullName;
	}

	/*
	* Метод, удаляющий вложенные теги по списку имен
	*
	* vector<string> names список имен удаляемых тегов
	*
	* Проходим по списку вложенных тегов, и если имя тега присутствует в списке, то удаляем его
	*/

	inline void eraseSubTagsByNames (vector<string> names) {
		vector<tag> result;
		
		unordered_set<string> del;
		for (auto it : names) del.insert (it);

		for (auto it : insideTags) {
			if (del.count (it.name)) continue;
			result.push_back (it);
		}
		insideTags.swap (result);
	}

	/*
	* Метод, удаляющий вложенные теги, если они не присутствуют в списке имен
	*
	* vector<string> names список имен тегов, которые мя не будем удалять
	*
	* Проходим по списку вложенных тегов, и если имя тега отсутствует в списке, то удаляем его
	*/

	inline void eraseSubTagsExceptNames (vector<string> names) {
		vector<tag> result;
		
		unordered_set<string> need;
		for (auto it : names) need.insert (it);

		for (auto it : insideTags) {
			if (!need.count (it.name)) continue;
			result.push_back (it);
		}
		insideTags.swap (result);
	}

	/*
	* Метод, приводящий тег в строковый формат
	*
	* string add добавление табуляции для вложенных тегов
	*
	* Генерируем строку для текущего тега в виде имени. Для каждого атрибутя генерируем строку вида 
	* "ключ значение". Если есть вложенные теги, то запускаемся рекурсивно увеличив табуляцию
	*/

	string toString (string add = "") const {
    		string res = add + "{\n" + add + "\t" + name + '\n';
    		for (auto it : attr) {
    			res += add + "\t" + it.first + " = " + it.second + '\n';
		}		
    		for (auto it : insideTags) {
    			res += it.toString (add + '\t');
    		}
    		res += add + "}\n";
    		return res;
	}

	/*
	* Оператор сравнения двух тегов
	*
	* const tag &T тег, с которым сравнивается текущий
	*
	* Сравниваем лексикографически имена. Если имена совпадают, то сравниваем по списку атрибутов. Если списки совпадают,
	* то сравниваем по списку вложенных тегов
	*/

	bool operator < (const tag &T) const {
		if (name != T.name) return name < T.name;	
		if (attr != T.attr) return attr < T.attr;
		return insideTags < T.insideTags;
	}

	/*
	* Оператор вывода тега
	*
	* ostream &cout поток вывода, const tag &t выводимый тег
	*
	* Приводим тег в строковое представление и выводим его в поток вывода
	*/

	friend ostream &operator << (ostream &cout, const tag &t) {
		cout << t.toString ();
		return cout;
	}
};


#endif 

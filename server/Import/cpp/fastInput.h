#ifndef fastInput
#define fastInput

#include <bits/stdc++.h>

using namespace std;

// Буферизованный ввод для ускорения считывания из файла.

// const int MAXBUF Размер буфера для ввода.

const int MAXBUF = 1e6;

struct bufferedReader {
	/*
	* FILE *inputStream Поток для ввода
	* char *buf Буфер для считывания
	* char *_cur Указатель на первый не обработанный символ в буфере
	* char *_end Указатель на конец буфера
	*/
	FILE *inputStream;

	char buf[MAXBUF + 1];
	char *_cur; 
	char *_end;

	// Конструктор по-умолчанию

	bufferedReader () {
		_cur = _end = NULL;
		inputStream = NULL;
	}

	/* 
	* Конструктор, принимающий поток ввода
	* FILE *input указанный поток ввода
	 */

	bufferedReader (FILE *input) {
		_cur = _end = NULL;
		inputStream = input;
	}

	/*
	* Конструктор, принимающий имя файла, из которого будем получать данные
	* const string &name имя файла
	*/

	bufferedReader (const string &name) {
		inputStream = fopen (name.c_str (), "r");
		_cur = _end = NULL;
	}

	/* 
	* Инициализация потока ввода
	* FILE *input новый поток ввода
	*/

	void init (FILE *input) {
		close ();
		inputStream = input;
		_cur = _end = NULL;
	}

	/*
	* Инициализация потока ввода
	* const string &name имя файла, который станет новым потоком ввода
	*/

	void init (const string &name) {
		close ();
		inputStream = fopen (name.c_str (), "r");
		_cur = _end = NULL;
	}

	// Деструктор

	~bufferedReader () {
		close ();
	}

	/*
	* Метод для закрытия потока ввода.
	*
	* Если поток ввода открыт, закрываем его.
	*/

	inline void close () {
		if (NULL != inputStream) fclose (inputStream);
		inputStream = NULL;
	}

	/*
	* Метод для заполнения буфера информацией.
	*
	* Считываем из потока ввода информацию (не более размера буфера).
	* Указателю на первый необработанный символ присваиваем начало буфера.
	* Указатель на конец буфера будет указывать на позицию после последнего прочитанного символа.
	*/
    
	inline void getBuf () {
		int len = fread (buf, sizeof (buf[0]), MAXBUF, inputStream);
		_cur = buf;
		if (len != MAXBUF) buf[len++] = EOF;
		_end = buf + len;		
	}

	/*
	* Получение текущего необработанного символа.
	*
	* Если буфер пуст, заполняем буфер.
	* Берем первый необработанный символ.
	*/

	inline char curChar () {
		if (_cur == _end) getBuf ();
		return *_cur;
	}

	/*
	* Получение текущего символа с удалением его из буфера.
	*
	* Если буфер пуст, заполняем буфер.
	* Берем первый необработанный символ.
	* Сдвигаем соответсвующий указатель.
	*/

	inline char getChar () {
		if (_cur == _end) getBuf ();
		char res = *_cur;
		_cur++;
		return res;
	}

	/*
	* Проверка, что в потоке ввода закончилась информация.
	*
	* Смотрим, что текущий необработанный символ равен EOF.
	*/

	inline bool checkEof () {
		if (_cur == _end) getBuf ();
		return *_cur == EOF;
	}

	/*
	* Проверка, что текущий символ в буфере -- буква.
	*
	* Смотрим, что текущий необработанный символ это буква.
	*/

	inline bool checkAlpha () {
		if (_cur == _end) getBuf ();
		return isalpha (*_cur);
	}

	/*
	* Проверка, что текущий символ в буфере -- двойная кавычка.
	*
	* Смотрим, что текущий необработанный символ это двойная кавычка.
	*/

	inline bool checkCommas () {
		if (_cur == _end) getBuf ();
		return '"' == *_cur;
	}

	/*
	* Проверка, что текущий символ в буфере -- пробел.
	*
	* Смотрим, что текущий необработанный символ это пробел.
	*/

	inline bool checkSpace () {
		if (_cur == _end) getBuf ();
		return isspace (*_cur);
	}

	/*
	* Считывание слова (последовательность букв)
	* string &t строка, в которую будет сохранено слово
	*
	* До тех пор, пока текущий символ в буфере не буква -- удаляем текущий символ из буфера
	* До тех пор, пока текущий символ в буфере буква -- конкатенируем его к ответу и удаляем из буфера
	*/

	inline void readWord (string &t) {    
		while (!checkAlpha ()) getChar ();
		t.clear ();
		while (checkAlpha ()) t += getChar ();
	}

	/*
	* Считывание токена (последовательность непробельных символов)

	* string &t строка, в которую будет сохранено слово
	*
	* До тех пор, пока текущий символ в буфере пробельный -- удаляем текущий символ из буфера
	* До тех пор, пока текущий символ в буфере непробельный -- конкатенируем его к ответу и удаляем из буфера
	*/

	inline void readToken (string &t) {
		while (checkSpace ()) getChar ();
		t.clear ();
		while (!checkEof () && !checkSpace ()) t += getChar ();
	}
};

#endif 

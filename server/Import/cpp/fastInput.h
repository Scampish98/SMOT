#ifndef fastInput
#define fastInput

#include <bits/stdc++.h>

using namespace std;

const int MAXBUF = 1e6;

struct bufferedReader {
	FILE *inputStream;

	char buf[MAXBUF + 1];
	char *_cur; 
	char *_end;

	bufferedReader () {
		_cur = _end = NULL;
		inputStream = NULL;
	}
	
	bufferedReader (FILE *input) {
		_cur = _end = NULL;
		inputStream = input;
	}
	
	bufferedReader (const string &name) {
		inputStream = fopen (name.c_str (), "r");
		_cur = _end = NULL;
	}

	void init (FILE *input) {
		close ();
		inputStream = input;
		_cur = _end = NULL;
	}

	void init (const string &name) {
		close ();
		inputStream = fopen (name.c_str (), "r");
		_cur = _end = NULL;
	}

	~bufferedReader () {
		close ();
	}

	inline void close () {
		if (NULL != inputStream) fclose (inputStream);
		inputStream = NULL;
	}
    
	inline void getBuf () {
		int len = fread (buf, sizeof (buf[0]), MAXBUF, inputStream);
		_cur = buf;
		if (len != MAXBUF) buf[len++] = EOF;
		_end = buf + len;		
	}

	inline char curChar () {
		if (_cur == _end) getBuf ();
		return *_cur;
	}

	inline char getChar () {
		if (_cur == _end) getBuf ();
		char res = *_cur;
		_cur++;
		return res;
	}

	inline bool checkEof () {
		if (_cur == _end) getBuf ();
		return *_cur == EOF;
	}

	inline bool checkAlpha () {
		if (_cur == _end) getBuf ();
		return isalpha (*_cur);
	}

	inline bool checkCommas () {
		if (_cur == _end) getBuf ();
		return '"' == *_cur;
	}

	inline bool checkSpace () {
		if (_cur == _end) getBuf ();
		return isspace (*_cur);
	}

	inline void readWord (string &t) {    
		while (!checkAlpha ()) getChar ();
		t.clear ();
		while (checkAlpha ()) t += getChar ();
	}

	inline void readToken (string &t) {
		while (checkSpace ()) getChar ();
		t.clear ();
		while (!checkEof () && !checkSpace ()) t += getChar ();
	}
};

#endif 

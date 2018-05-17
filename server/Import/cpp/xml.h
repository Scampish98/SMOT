#ifndef XML
#define XML

#include <bits/stdc++.h>
#include "fastInput.h"

using namespace std;

const string nullName = "someRandomeNameForNullName";

void readTag (bufferedReader &input, string &t) {
	t.clear ();
	while (!input.checkEof () && input.curChar () != '<') input.getChar ();
	if (input.checkEof ()) return;
	input.getChar ();
	while (input.curChar () != '>') t += input.getChar ();
	input.getChar ();
}

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

struct tag {
    string name;
    map<string, string> attr;
	vector<tag> insideTags;

	tag () {
		name = nullName;
	}

	vector<tag> findSubtagByName (const string &name) {
		vector<tag> res;
		for (auto it : insideTags) {
			if (it.name == name) res.push_back (it);
		}
		return res;
	}

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

	void deleteAttr (const string &t) {
		auto it = attr.find (t);
		if (it == attr.end ()) return;
		attr.erase (it);
	}

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

	inline bool checkEmpty () const {
		return name == nullName;
	}

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

	bool operator < (const tag &T) const {
		if (name != T.name) return name < T.name;	
		if (attr != T.attr) return attr < T.attr;
		return insideTags < T.insideTags;
	}

	friend ostream &operator << (ostream &cout, const tag &t) {
		cout << t.toString ();
		return cout;
	}
};


#endif 

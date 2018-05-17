#include "fastInput.h"
#include "xml.h"

typedef long double dbl;

const dbl eps = 1e-9;
const dbl pi = acosl (-1.0);

inline bool doubleEqual (dbl a, dbl b) {
	return abs (a - b) < eps;
}

struct point {
	dbl lat, lon;

	point () {}
	point (dbl lat, dbl lon) : lat (lat), lon (lon) {}

	dbl toRadian (dbl a) {
		return a * pi / 180;
	}

	dbl geoDist (point p) {
		dbl phi1 = toRadian (lat);
		dbl phi2 = toRadian (p.lat);
		dbl l1 = toRadian (lon);
		dbl l2 = toRadian (p.lon);
		dbl d = acos (sin (phi1) * sin (phi2) + cos (phi1) * cos(phi2) * cos (l1 - l2));
		return d * 6371;
	}
	bool operator == (const point &p) const {
		return doubleEqual (lat, p.lat) && doubleEqual (lon, p.lon);
	}
};

bufferedReader input;

unordered_map<string, tag> node, way, relation;
unordered_set<string> stops;

vector<string> pth[9][2], stp[9][2];
int cnt[9];


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

inline tag getTag (tag T) {
	string id = T.attr["ref"];
	string type = T.attr["type"];

	if (type == "node") return node[id];
	else if (type == "way") return way[id];
	else return relation[id];
}

point last;
unordered_set<string> was;

inline point getPoint (const string &id) {
	auto nd = node[id];
	return point (atof (nd.attr["lat"].c_str ()), atof (nd.attr["lon"].c_str ()));
}

inline void addWay (tag T, vector<string> &ids) {
	if (ids.empty ()) was.clear ();
	auto list = T.findSubtagByName ("nd");
	vector<string> I;
	for (auto it : list) {
		I.push_back (it.attr["ref"]);
	}
	point p1 = getPoint (I.front ());
	point p2 = getPoint (I.back ());
	if (last.geoDist (p1) > last.geoDist (p2)) reverse (I.begin (), I.end ());

	auto cp = I;
	I.clear ();
	for (auto it : cp) {
		if (was.count (it)) I.clear ();
		else I.push_back (it);
	}

	was.clear ();
	for (auto it : I) { 
		was.insert (it);
		ids.push_back (it);
	}
	last = getPoint (ids.back ());
}

inline int stupidInsertPoint (vector<string> &list, const string &pointId) {
	int n = (int) list.size ();
	for (int i = 0; i < n; i++) {
		if (list[i] == pointId) return i;
	}
	return n;
} 

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

int getF (tag T) {
	auto list = T.findSubtagByName ("tag");
	for (auto it : list) {
		if (it.attr["k"] == "ref") return atoi (it.attr["v"].c_str ());
	}
	return -1;
}

inline void addRelation (tag T) {
	auto list = T.findSubtagByName ("member");

	int f = getF (T);
	if (f < 0) return;
	int s = cnt[f]++;

	for (auto it : list) {
		it = getTag (it);
		string id = it.attr["id"];
		if (it.name == "node") {
			stp[f][s].push_back (id);
			stops.insert (id);
		}
	}
	last = getPoint (stp[f][s].front ());
	for (auto it : list) {
		it = getTag (it);
		string id = it.attr["id"];
		if (it.name == "way") {
			addWay (it, pth[f][s]);		
		}
	}
	insertStops (pth[f][s], stp[f][s]);
}

int main () {
	double st = clock ();
	load ();

	assert (freopen ("myFile.osm", "w", stdout));

	for (auto it : relation) {
		addRelation (it.second);
	}

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

	cout << node.size () << endl;
	for (auto it : node) {
		tag temp = it.second;
		string res = temp.attr["id"] + " " + temp.attr["lat"] + " " + temp.attr["lon"];
		puts (res.c_str ());
	}

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

	printf ("%d\n", (int) stops.size ());

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

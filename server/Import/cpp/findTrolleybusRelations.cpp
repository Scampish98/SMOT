#include "fastInput.h"
#include "xml.h"

bufferedReader input;

unordered_map<string, tag> node, way, relation, resultNode, resultWay, resultRelation;
unordered_set<string> wasRelation, wasWay;

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

inline tag getTag (tag T) {
	string id = T.attr["ref"];
	string type = T.attr["type"];

	if (type == "node") return node[id];
	else if (type == "way") return way[id];
	else return relation[id];
}

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
	load ();

	input.init ("RU-KR.osm");
	assert (freopen ("myFile.osm", "w", stdout));

	tag temp;

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

	input.close ();

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

#include "fastInput.h"
#include "xml.h"

vector<string> nodesID;
vector<string> lat, lon;
unordered_map<string, vector<int> > pos;

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

bufferedReader input;

int main () {
	double st = clock ();
	load ();
	
	input.init ("RU-KR.osm");
	assert (freopen ("PtzBounds.txt", "w", stdout));

	tag temp;

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

	input.close ();

	cout << nodesID.size () << '\n';
	for (int i = 0; i < (int) nodesID.size (); i++) {
		cout << lat[i] << ' ' << lon[i] << '\n'; 
	}

    clog << (clock () - st) / CLOCKS_PER_SEC << endl;

	return 0;
}

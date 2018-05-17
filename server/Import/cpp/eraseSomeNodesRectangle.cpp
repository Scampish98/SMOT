#include "fastInput.h"
#include "xml.h"

string mnLon, mxLon, mnLat, mxLat;

string sToD (string t) {
	string a, b;
	for (auto it : t) {
		if (it == '.') {
			a.swap (b);
			continue;
		}
		b += it;
	}
	while (a.size () < 5u) a = "0" + a;
	while (b.size () < 10u) b += '0';
	return a + b;
}

void load () {
	mnLat = mnLon = string (15, '9');
	mxLat = mxLon = string (15, '0');
	string lat, lon;
	ifstream in;
	in.open ("PtzBounds.txt", std::ifstream::in);

	int n;
	in >> n;

	for (int i = 0; i < n; i++) {
		in >> lat >> lon;
		lat = sToD (lat);
		lon = sToD (lon);
		mnLat = min (mnLat, lat);
		mxLat = max (mxLat, lat);
		mnLon = min (mnLon, lon);
		mxLon = max (mxLon, lon);
	}

	in.close ();
}

bufferedReader input;

int main () {
	double st = clock ();

	load ();
	
	input.init ("RU-KR.osm");
	assert (freopen ("myFile.osm", "w", stdout));

	tag temp;

	while (!input.checkEof ()) {
		temp.initTag (input);
		if (temp.name == "node") {            
			string lat = sToD (temp.attr["lat"]);
			string lon = sToD (temp.attr["lon"]);

			if (lat < mnLat || lat > mxLat) continue;
			if (lon < mnLon || lon > mxLon) continue;
		}
		if (!temp.checkEmpty ()) puts (temp.toXml ().c_str ());
	}

	input.close ();

	clog << (clock () - st) / CLOCKS_PER_SEC << endl;

	return 0;
}

#include "fastInput.h"
#include "xml.h"

const string wayID = "60388877";

bufferedReader input;

int main () {
	double st = clock ();
	
	input.init ("RU-KR.osm");
	assert (freopen ("PtzID.txt", "w", stdout));

	vector<string> ans;
	tag temp;

	while (!input.checkEof ()) {
		temp.initTag (input);
		if (temp.name != "way") continue;
		if (temp.attr["id"] != wayID) continue;
		auto res = temp.findSubtagByName ("nd");
		for (auto it : res) ans.push_back (it.attr["ref"]);
        	break;
	}

	input.close ();

	cout << ans.size () << endl;
	for (auto it : ans) cout << it << endl;

	clog << (clock () - st) / CLOCKS_PER_SEC << endl;

	return 0;
}

#include "fastInput.h"
#include "xml.h"

bufferedReader input;

int main () {
	double st = clock ();
	
	input.init ("RU-KR.osm");
	assert (freopen ("myFile.osm", "w", stdout));

	{
		string test;          
		readTag (input, test);
		readTag (input, test);
	}

	tag temp;

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

	input.close ();

	clog << (clock () - st) / CLOCKS_PER_SEC << endl;

	return 0;
}

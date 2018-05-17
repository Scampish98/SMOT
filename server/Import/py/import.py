import sys
import urllib
import os

path = 'http://data.gis-lab.info/osm_dump/dump/latest/RU-KR.osm.bz2'
destination = 'RU-KR.osm.bz2'

print ('download \"RU-KR.osm.bz2\" start')
urllib.urlretrieve (path, destination)
print ('download \"RU-KR.osm.bz2\" end')

result = os.popen ('ls -l').read ().split ('\n')
for i in result :
	temp = i.split ()
	print (temp)
	if temp[-1] == 'RU-KR.osm.bz2' :
		result = int (temp[4])
		break
if result < 1024 :
	sys.exit (1)

print ('download images start')
result = os.system ('python downloadImage.py')
if result != 0 :
	print ('Download image error!')
	sys.exit (1)
print ('download images end')

print ('unzip \"RU-KR.osm\" start')
os.system ('bzip2 -d ' + destination)
print ('unzip \"RU-KR.osm\" end')

os.system ('python recompile.py')

print ('eraseSomeInformation in \"RU-KR.osm\" start')
os.system ('./eraseSomeInformation')
os.system ('mv myFile.osm RU-KR.osm')
print ('eraseSomeInformation in \"RU-KR.osm\" end')

print ('findPtzPolygon in \"RU-KR.osm\" start')
os.system ('./findPtzPolygon')
print ('findPtzPolygon in \"RU-KR.osm\" end')

print ('findBounds in \"RU-KR.osm\" start')
os.system ('./findBounds')
print ('findBounds in \"RU-KR.osm\" end')

print ('eraseSomeNodesRectangle in \"RU-KR.osm\" start')
os.system ('./eraseSomeNodesRectangle')
os.system ('mv myFile.osm RU-KR.osm')
print ('eraseSomeNodesRectangle in \"RU-KR.osm\" end')

print ('findTrolleybusRelations in \"RU-KR.osm\" start')
os.system ('./findTrolleybusRelations')
os.system ('mv myFile.osm RU-KR.osm')
print ('findTrolleybusRelations in \"RU-KR.osm\" end')

print ('buildPathsAndStops in \"RU-KR.osm start')
os.system ('./buildPathsAndStops')
os.system ('mv myFile.osm ../result.data')
print ('buildPathsAndStops in \"RU-KR.osm\" end')

os.system ('python clear.py')

sys.exit (0)

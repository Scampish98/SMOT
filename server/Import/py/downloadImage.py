import sys
import urllib
import os

path = '../../../images'
tempPath = 'tempImages'

os.system ('mkdir ' + tempPath)

urls = [ 'http://www.ptz-trans.ru/images/597.jpg', 'http://www.ptz-trans.ru/images/598.jpg', 'http://www.ptz-trans.ru/images/599.jpg', 'http://www.ptz-trans.ru/images/600.jpg', 'http://www.ptz-trans.ru/images/601.jpg', 'http://www.ptz-trans.ru/images/602.jpg', 'http://www.ptz-trans.ru/images/603.jpg' ]
numbers = [1, 2, 3, 4, 5, 6, 8]

destination = 'route_'

for i in range (len (numbers)) :
	dest = tempPath + '/' + destination + str (numbers[i]) + '.jpg'
	urllib.urlretrieve (urls[i], dest)

result = os.popen ('ls -l ' + tempPath + '/').read ().split ('\n')
for i in range (len (numbers)) :
	temp = result[i + 1].split ()
	temp = int (temp[4])
	if (temp < 1024) :
		sys.exit (1)

os.system ('rm -r ' + path)
os.system ('cp -r ' + tempPath + ' ' + path)
os.system ('rm -r ' + tempPath)

sys.exit (0)

import sys
import urllib
import os

# Скачиваем архив с геоданными.
# Если архив не скачался, то завершаемся с кодом возврата 1.
# Иначе вызываем скрипт для загрузки изображений.
# Если изображения не удалось загрузить (скрипт завершился с ненулевым кодом возврата),
# то завершаемся с кодом возврата 1.
# Иначе распаковываем архив.

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

# Компилируем все файлы *.cpp

os.system ('python recompile.py')

# Удаляем из файла с данными часть описания данных, которая нас не интересует.
# Размер файла уменьшится почти в два раза, что позволит работать с ним гораздо быстрее.

print ('eraseSomeInformation in \"RU-KR.osm\" start')
os.system ('./eraseSomeInformation')
os.system ('mv myFile.osm RU-KR.osm')
print ('eraseSomeInformation in \"RU-KR.osm\" end')

# Находим информацию о точках многоугольника, задающего условные границы Петрозаводска.
# Сохраняем информацию в файле PtzID.txt

print ('findPtzPolygon in \"RU-KR.osm\" start')
os.system ('./findPtzPolygon')
print ('findPtzPolygon in \"RU-KR.osm\" end')

# Получаем координаты многоугольника условной границы Петрозаводска.
# Сохраняем их в файле PtzBounds.txt

print ('findBounds in \"RU-KR.osm\" start')
os.system ('./findBounds')
print ('findBounds in \"RU-KR.osm\" end')

# По координатам многоугольника получаем прямоугольник, содержащий Петрозаводск.
# Все точки, не попадающие в этот прямоугольник, выкидываем.
# Теперь размер файла позволяет держать его полностью в оперативной памяти без значительной нагрузки на систему.

print ('eraseSomeNodesRectangle in \"RU-KR.osm\" start')
os.system ('./eraseSomeNodesRectangle')
os.system ('mv myFile.osm RU-KR.osm')
print ('eraseSomeNodesRectangle in \"RU-KR.osm\" end')

# Находим отношения, описывающие троллейбусные маршруты.
# Оставляем в файле эти отношения, а также пути и точки, участвующие в этих отношениях.
# Остальную информацию удаляем.

print ('findTrolleybusRelations in \"RU-KR.osm\" start')
os.system ('./findTrolleybusRelations')
os.system ('mv myFile.osm RU-KR.osm')
print ('findTrolleybusRelations in \"RU-KR.osm\" end')

# Перестраиваем информацию о маршрутах в удобный для обработки формат.
# Сохраняем данные в файл result.data

print ('buildPathsAndStops in \"RU-KR.osm start')
os.system ('./buildPathsAndStops')
os.system ('mv myFile.osm ../result.data')
print ('buildPathsAndStops in \"RU-KR.osm\" end')

# Зачищаем все временные файлы и завершаемся с кодом возврата 0. 

os.system ('python clear.py')

sys.exit (0)

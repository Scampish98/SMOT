# -*- coding: utf-8 -*-
import os

# Принудительно перекомпилируем все файлы *.cpp

os.system ('touch eraseSomeInformation.cpp')
os.system ('touch findPtzPolygon.cpp')
os.system ('touch findBounds.cpp')
os.system ('touch eraseSomeNodesRectangle.cpp')
os.system ('touch findTrolleybusRelations.cpp')
os.system ('touch buildPathsAndStops.cpp')

os.system ('python compile.py')

# Testscript TYPO3 EXT:fontawesomeplus - <info@netweiser.com>

import sys
# try to load fontforge
try:
	import fontforge
	print ("1")
except ImportError:
	print ("0")
# try to load json
try:
	import json
	print ("1")
except ImportError:
	print ("0")
# get Python Version
print(sys.version)
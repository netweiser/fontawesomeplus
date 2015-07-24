#!/usr/bin/python
# coding:ISO-8859-1
import sys, fontforge, json

fontpathimport = sys.argv[1]
svgimport = json.loads(sys.argv[2])
fontnameimport = sys.argv[3]
commentimport = sys.argv[4]
copyrightimport = sys.argv[5]
versionimport = sys.argv[6]
savedirimport = sys.argv[7]
font = fontforge.open(fontpathimport)
for key,value in svgimport.items():
	unistring,name = value.split(',')
	uni = int(unistring)
	glyph = font.createChar(uni,name)
	glyph.importOutlines(key)
font.familyname = fontnameimport
font.fontname = fontnameimport
font.fullname = fontnameimport
font.comment = commentimport
font.copyright = copyrightimport
font.version = versionimport
font.generate(savedirimport+fontnameimport+".otf")
font.generate(savedirimport+fontnameimport+".eot")
font.generate(savedirimport+fontnameimport+".woff")
font.generate(savedirimport+fontnameimport+".woff2")
font.generate(savedirimport+fontnameimport+".ttf")
font.generate(savedirimport+fontnameimport+".svg")
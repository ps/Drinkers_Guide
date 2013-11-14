#!/usr/bin/python
import re
import urllib
import json

states = ["NJ", "NY", "PA"]
index = 0;
inFile = open(states[index] + ".txt", "r")

outFile = open(states[index] + "_complete.txt", "w");
i = 0

for line in inFile:
	parts = line.split(";")
	repSp = re.compile('\s')
	city = repSp.sub("+", parts[0])
	finalStr = city + "," + states[index]
	#url = "http://maps.google.com/maps/api/geocode/json?address=%s&sensor=false" % finalStr
	url = "http://maps.google.com/maps/api/geocode/json?address=Clifton,NJ&sensor=false"
	out = urllib.urlopen(url)
	content = out.read()
	jObj = json.loads(content)
	print jObj["results"]
	if len(jObj) > 0:
		#take the first
		lat = jObj["results"][0]["geometry"]["location"]["lat"]
		lon = jObj["results"][0]["geometry"]["location"]["lng"]
		outFile.write("%d;%d;%s;%s" % (lat, lon, parts[0], parts[1]))
		print "Finished %d" % i
	i += 1
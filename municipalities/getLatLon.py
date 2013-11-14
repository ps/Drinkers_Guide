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
	url = "http://maps.google.com/maps/api/geocode/json?address=%s&sensor=false" % finalStr

	count = 0 #if after 15 tries we do not get data, ignore this city
	content = ""
	jObj = None
	hasData = False
	while not hasData and count < 15:
		out = urllib.urlopen(url)
		content = out.read()
		out.close()
		jObj = json.loads(content)
		if "results" in jObj and len(jObj["results"]) > 0:
			hasData = True

	if hasData:
		#take the first
		lat = jObj["results"][0]["geometry"]["location"]["lat"]
		lon = jObj["results"][0]["geometry"]["location"]["lng"]
		outFile.write("%f;%f;%s;%s" % (lat, lon, parts[0], parts[1]))
		print "Finished %d" % i
	else:
		print "No data for %s" % parts[0]

	i += 1

inFile.close()
outFile.close()
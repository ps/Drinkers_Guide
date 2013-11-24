#!/usr/bin/python
'''
Uses actual municipalities from New Jersey and uses the Google Maps API Geocoding service to find the latitude and longitude. 
For each person/bar it will randomize the longitude/latitude by ~10 miles
'''
import re
import urllib
import json
import random
from settings import *

data = None
states = ["NJ"]

def generate():
	global states, datadir
	index = 0;
	inFile = open(datadir + states[index] + ".txt", "r")

	outFile = open(datadir + states[index] + "_complete.txt", "w");
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

#reads the city data into the data dict
#each list in the data dict is:
# (city, state, lat, long, rangeStart, rangeEnd)
# rangeStart/rangeEnd - values between 0 and 1 for which this city will appear in the probability

def loadData():
	global data, states, datadir
	data = []
	total = 0
	#load up the files
	for key in states:
		#open file
		f = open(datadir + key + "_complete.txt", "r")
		for line in f:
			parts = line.split(";")
			total += float(parts[3])
			data.append([parts[2], key, float(parts[0]), float(parts[1]), float(parts[3]), -1])
	#now let's use the total to calculate the range
	cur = 0.0 #cur part in range
	for l in data:
		val = l[4] / total
		l[4] = cur
		l[5] = cur + val
		cur += val

#returns a tuple with city,state,lat,lon
#the lat/lon are varied by .15
def getCity():
	global data, datadir
	#first get random state
	if(data == None):
		#need to load the data
		loadData()
	r = random.random()
	#TODO maybe optimize by doing linear search but now time is a factor so screw it
	for l in data:
		if r > l[4] and r < l[5]:
			#we have a winner
			varLon = random.random() * .3 - .15
			varLat = random.random() * .3 - .15
			return (l[0], l[1], l[2] + varLat, l[3] + varLon)
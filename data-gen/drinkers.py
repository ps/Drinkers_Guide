import random
import urllib
import re
import math
from bs4 import BeautifulSoup
import cities

#get street names by posting first name to Wu Tang Name generator site 
def getStreet(givenName):
	url = 'http://www.mess.be/inickgenwuname.php'
	params = urllib.urlencode( {"realname": givenName})
	f = urllib.urlopen(url,params)
	content = f.read()
	soupCnt = BeautifulSoup(content)
	fontTags = soupCnt.findAll("font")

	name = "%s" % (fontTags[1])
	name = "%s" % (name[15:-8])
	name = re.sub(r'\W+', ' ', name)
	return name

#read first names from file and put them into first list
f = open('first.txt')
lines=f.readlines()
f.close()

#first[0]: gender
#first[1]: some random thing
#first[2]: first name
first = []
for item in lines:
	first.append( item.split(" "))

for item in first:
	if item[0]=="MO":
		item[0]="M"
	else:
		item[0]="F"
	#strip random newlines from the end
	item[2] = re.sub(r'\W+', '', item[2])

	#make first name proper capitalization
	isFirst = True
	tempName = item[2]
	item[2] = ""
	for c in tempName:
		if isFirst:
			item[2] = c
			isFirst=False
		else:
			item[2] = item[2] + c.lower()

#read in last names from file
f = open('last_names.csv')
lines2 = f.read()
#last list holds last names read in from the CSV
last = lines2.split(";")
f.close()

print len(last);

#used to make sure that no last name is repeated
usedLastNames = []

#number used to indicate the number of entries you want
number = 10

stAbbr = ['Aly','Ave','Blvd','Cir','Ct','Dr','Ln','Rd','St','Way']

#most names came out to be female so changed it so that only 20% of generated names are females
males = math.ceil(number*0.8)
females = math.ceil(number*0.2)
for i in range(0,number):	
	lastNum = random.randint(0,len(last)-1)
	firstNum = random.randint(0,len(first)-1)
	while True:
		if lastNum not in usedLastNames:
			break
		else:	
			lastNum = random.randint(0,len(last)-1)

	if first[firstNum][0]=="F" and females==0:
		firstNum = random.randint(0,len(first)-1)
		while True:
			if first[firstNum][0]=="F":
				firstNum = random.randint(0,len(first)-1)
			else:
				break
	
	usedLastNames.append(lastNum)
	randFirst = first[firstNum][2]
	randGender = first[firstNum][0]
	randLast = last[lastNum]
	if randGender=="F":
		females = females-1
	randAge = random.randint(17,80)
	randPhone = "%i-%i-%i" % (random.randint(100,999),random.randint(100,999), random.randint(1000,9999))
	randStreet = "%i %s %s" % (random.randint(1,600), getStreet(randFirst), stAbbr[random.randint(0,len(stAbbr)-1)])
	print "%i. %s %s [%s] Age: %i Phone: %s Addr: %s" % (i,randFirst, randLast,randGender,randAge, randPhone, randStreet)

from settings import *
import re
import random
import sys 
'''
This file reinserts female drinkers to the database with truncated number of drinkers and bars.

The dump before this change was made is called dumpBeforeFemales.sql
'''
cxn = conn_remote()

#read first names from file and put them into first list
f = open(datadir + 'first.txt')
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

femFirst = []
for name in first:
	if name[0]=="F":
		femFirst.append(name[2])


#read in last names from file 
f = open(datadir + 'last_names.csv')
lines2 = f.read()
#last list holds last names read in from the CSV
last = lines2.split(";")
f.close()


#get drinkers
c = cxn.cursor()
c.execute("SELECT * FROM Drinker")
drinkers = c.fetchall()
nDrinkers = len(drinkers)
c.close()

#number of names to replace
replace=1500

#used last names
usedLast = []

usedReplaceVictims = []
while True:
	if replace==0:
		print "DONE!"
		break

	print replace

	#generate a random female name
	lastNum = random.randint(0,len(last)-1)
	firstNum = random.randint(0,len(femFirst)-1)
	while True:
		if lastNum not in usedLast:
			break
		else:	
			lastNum = random.randint(0,len(last)-1)
	usedLast.append(lastNum)
	finalName = "%s %s" %  (femFirst[firstNum], last[lastNum])

	replace = replace -1

	#pick a random male name from db to replace
	replaceVictim = drinkers[random.randint(0,len(drinkers)-1)]
	while True:
		if replaceVictim[1]=="F":
			replaceVictim = drinkers[random.randint(0,len(drinkers)-1)]
		else:
			break
	
	while True:
		if replaceVictim[0] not in usedReplaceVictims:
			break
		else:
			replaceVictim = drinkers[random.randint(0,len(drinkers)-1)]

	replaceVictim = replaceVictim[0]
	usedReplaceVictims.append(replaceVictim)


	#replace name in Drinkers 
	print "Replacing names in Drinkers table..."
	ge = "F"

	q = """UPDATE Drinker set name=%s , gender=%s  WHERE name=%s """
	c = cxn.cursor()
	try:
		c.execute(q, (finalName,ge,replaceVictim))
	except MySQLdb.Error, e:
		print e
	cxn.commit()
	c.close()

	#replace name in Frequents 
	print "Replacing names in Frequents table..."
	
	q = """UPDATE Frequents set drinker=%s WHERE drinker=%s """
	c = cxn.cursor()
	try:
		c.execute(q, (finalName,replaceVictim))
	except MySQLdb.Error, e:
		print e
	cxn.commit()
	c.close()

		
	#replace the name in Likes 	
	print "Replacing names in Likes table..."
	
	q = """UPDATE Likes set drinker=%s WHERE drinker=%s """
	c = cxn.cursor()
	try:
		c.execute(q, (finalName,replaceVictim))
	except MySQLdb.Error, e:
		print e
	cxn.commit()
	c.close()

cxn.close()

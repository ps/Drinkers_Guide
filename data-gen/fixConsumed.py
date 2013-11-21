import MySQLdb
import re
import random
import sys 
'''
This file reinserts female drinkers to the database with truncated number of drinkers and bars.

The dump before this change was made is called dumpBeforeFemales.sql
'''

def conn_remote():
	db = MySQLdb.connect(host="cs336-9.cs.rutgers.edu", user="root", passwd="root", db="SAFETY_DB")
	print "Connection made"
	return db
print "Getting connection"
cxn = conn_remote()

print "Getting data"
#get drinkers
c = cxn.cursor()
c.execute("SELECT * FROM Consumed WHERE numDrinks='0'")
drinkers = c.fetchall()
nDrinkers = len(drinkers)
c.close()


print "Updating yo"
for i in drinkers:
	print nDrinkers
	nDrinkers = nDrinkers -1
	newDrinks = random.randint(1,10)
	q = """UPDATE Consumed set numDrinks=%s WHERE drinker=%s and bar=%s and dateOfConsump=%s """
	c = cxn.cursor()
	try:
		c.execute(q, (newDrinks,i[0],i[1],i[2]))
	except MySQLdb.Error, e:
		print e
		sys.exit()
	cxn.commit()
	c.close()

print "Done yo"
sys.exit()
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

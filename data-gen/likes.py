from settings import *
import random
import sys

'''
This generation file assumes that the database already has the drinkers as well as the beers already in place.

Also, this does not check for duplicate entries, but relies on the SQL primary key restraints to prevent duplicates.
'''
cxn = conn_remote()
number = 15000 #number of likes rows desired

#get drinkers


print "Fetching drinkers... please wait"
c = cxn.cursor()
c.execute("SELECT * FROM Drinker")
drinkers = c.fetchall()
nDrinkers = len(drinkers)
c.close()
print "Fetching international beers"
c = cxn.cursor()
c.execute("SELECT * FROM Beer where Manf in (SELECT name FROM Manufacturer Where country = 'Poland');")
ibeers = c.fetchall()
nibeers = len(ibeers)
c.close()
print "Fetching all other beers... please wait"
c = cxn.cursor()
c.execute("SELECT * FROM Beer where Manf not in (SELECT name FROM Manufacturer Where country = 'Poland');")
beers = c.fetchall()
nBeers = len(beers)
c.close()

#randomly assign drinkers to beers, favor the polish ones so they appear 40% of the time
count = 0 
rowCount = 0
print "Inserting..."
for i in range(number):
	rand = random.random()
	beer = None
	if(rand < .4):
		#pick from polish
		beer = ibeers[random.randint(0, nibeers-1)]
	else:
		#pick from regular beers
		beer = beers[random.randint(0, nBeers-1)]
	drinker = drinkers[random.randint(0, nDrinkers-1)]

	q = "INSERT INTO Likes (`drinker`, `beer`) VALUES(\"%s\", \"%s\")" % (drinker[0], beer[0])
	
	rowCount += 1
	if rowCount > 1000:
		count += 1
		print "Done %d thousand" % count
		rowCount = 0
	try:
		cxn.query(q)
	except MySQLdb.Error, e:
	    try:
	        print "MySQL Error [%d]: %s" % (e.args[0], e.args[1])
	    except IndexError:
	        print "MySQL Error: %s" % str(e)
#insert them into the likes table

cxn.close()

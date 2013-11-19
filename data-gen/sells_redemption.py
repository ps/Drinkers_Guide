from settings import *
import random
import sys

'''
This generation file assumes that the database already has the bars as well as the beers already in place.

Also, this does not check for duplicate entries, but relies on the SQL primary key restraints to prevent duplicates.

'''
cxn = conn_remote()

#get bars


print "Fetching bars... please wait"
c = cxn.cursor()
c.execute("SELECT name From Bar WHERE name Not in (Select bar from Sells);")
bars = c.fetchall()
nBars = len(bars)
c.close()
print "Fetching beers"
c = cxn.cursor()
c.execute("SELECT name FROM Beer")
beers = c.fetchall()
nBeers = len(beers)
c.close()

#randomly assign beers to bars, favor the polish ones so they appear 40% of the time
#have illegal beers maybe 1% of the time
count = 0 
rowCount = 0
print "Inserting..."
for i in range(nBars):
	print count
	rand = random.random()
	bar = bars[i]
	beer = beers[random.randint(0, nBeers-1)]

	q = "INSERT INTO Sells (`bar`, `beer`) VALUES(\"%s\", \"%s\")" % (bar[0], beer[0])
	
	rowCount += 1
	if rowCount > 1000:
		count += 1
		print "Done %d thousand" % count
		rowCount = 0
	'''try:
		cxn.query(q)
	except MySQLdb.Error, e:
	    try:
	        print "MySQL Error [%d]: %s" % (e.args[0], e.args[1])
	    except IndexError:
	        print "MySQL Error: %s" % str(e)'''
#insert them into the sells table

cxn.close()

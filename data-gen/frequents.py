from settings import *
import random
'''
This generation file assumes that the database already has the drinkers as well as the beers already in place.

Also, this does not check for duplicate entries, but relies on the SQL primary key restraints to prevent duplicates.
'''
cxn = conn_remote()
number = 100000 #number of frequents rows desired

#get drinkers


print "Fetching drinkers... please wait"
c = cxn.cursor()
c.execute("SELECT * FROM Drinker")
drinkers = c.fetchall()
nDrinkers = len(drinkers)
c.close()
print "Fetching international bars"
c = cxn.cursor()
c.execute("SELECT * FROM Bar WHERE international=1")
ibars = c.fetchall()
nIbars = len(ibars)
c.close()
print "Fetching all other bars... please wait"
c = cxn.cursor()
c.execute("SELECT * FROM Bar WHERE international=0")
bars = c.fetchall()
nBars = len(bars)
c.close()

#randomly assign drinkers to bars, favor the polish ones so they appear 40% of the time

print "Inserting..."
for i in range(number):
	rand = random.random()
	bar = None
	if(rand < .4):
		#pick from polish
		bar = ibars[random.randint(0, nIbars-1)]
	else:
		#pick from regular bars
		bar = bars[random.randint(0, nBars-1)]
	drinker = drinkers[random.randint(0, nDrinkers-1)]

	q = "INSERT INTO Frequents (`drinker`, `bar`) VALUES(\"%s\", \"%s\")" % (drinker[0], bar[6])
	try:
		cxn.query(q)
	except MySQLdb.Error, e:
	    try:
	        print "MySQL Error [%d]: %s" % (e.args[0], e.args[1])
	    except IndexError:
	        print "MySQL Error: %s" % str(e)

#insert them into the likes table

cxn.close()

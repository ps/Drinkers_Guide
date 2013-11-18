from settings import *
import random
'''
Updated as of 11/16:
Now people frequent bars in their own city, but they have a small probility of having frequented up to five bars in other (random) cities.
This is so the data is more realistic.

I created a temporary index called 'tmp_speedup' on the Bars city column

This generation file assumes that the database already has the drinkers as well as the bars already in place.

Also, this does not check for duplicate entries, but relies on the SQL primary key restraints to prevent duplicates.
'''
cxn = conn_remote()
numPerDrinker = 3 #max number of bars frequented by a drinker in their own city (other cities can add up to 2 more)

#get drinkers


print "Fetching drinkers... please wait"
c = cxn.cursor()
c.execute("SELECT * FROM Drinker")
drinkers = c.fetchall()
nDrinkers = len(drinkers)
c.close()
print "Fetching bars... please wait"
c = cxn.cursor()
c.execute("SELECT * FROM Bar")
bars = c.fetchall()
nBars = len(bars)
c.close()

print "Creating dict..."
lookup = {} #city: list of bars
for r in bars:
	if r[0] not in lookup:
		lookup[r[0]] = []
	lookup[r[0]].append(r[6])
#randomly assign drinkers to bars
count = 0 
rowCount = 0
print "Inserting..."
for drinker in drinkers:
	nLocalBars = 0
	if drinker[4] in lookup:
		localBars = lookup[drinker[4]]
		nLocalBars = len(localBars)
		n = int(random.random() * (numPerDrinker+1))
		if n == 0:
			n = 1
		if(nLocalBars < n):
			n = nLocalBars
		if nLocalBars > 0:
			for j in range(n):
				#select random bar from that city
				randBar = localBars[random.randint(0, nLocalBars-1)]
				q = "INSERT INTO Frequents (`drinker`, `bar`) VALUES(\"%s\", \"%s\")" % (drinker[0], randBar)
				try:
					cxn.query(q)
				except MySQLdb.Error, e:
				    try:
				        print "MySQL Error [%d]: %s" % (e.args[0], e.args[1])
				    except IndexError:
				        print "MySQL Error: %s" % str(e)
    #small change of frequenting two random bars
	for k in range(2):
		chance = .1
		if nLocalBars == 0:
			chance = 1
		if random.random() < chance:
			#add random bar
			randBar = bars[random.randint(0, nBars-1)]
			q = "INSERT INTO Frequents (`drinker`, `bar`) VALUES(\"%s\", \"%s\")" % (drinker[0], randBar[6])
			try:
				cxn.query(q)
			except MySQLdb.Error, e:
			    try:
			        print "MySQL Error [%d]: %s" % (e.args[0], e.args[1])
			    except IndexError:
			        print "MySQL Error: %s" % str(e)
			
		else:
			break
		if nLocalBars == 0:
			break

	rowCount += 1
	if rowCount >= 100:
		count += 1
		print "Done %d drinkers" % (count * 100)
		rowCount = 0
#insert them into the frequents table

cxn.close()

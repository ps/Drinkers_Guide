from settings import *
import random
import sys

'''
Have two drinkers who frequent the same bar leave with each other.

I think the best approach is to avoid multiple queries (e.g. frequents is taking forever) so I am going to get everything from Frequents sorted by the bar and then choose randomly from that

This generation file assumes that the database already has the drinkers.

Also, this does not check for duplicate entries, but relies on the SQL primary key restraints to prevent duplicates.
'''
cxn = conn_remote()
number = 5000 #number of rows desired

#get drinkers


print "Fetching frequents... please wait"
c = cxn.cursor()
c.execute("SELECT * FROM Frequents ORDER BY bar")
freqs = c.fetchall()
nFreqs = len(freqs)
c.close()

#randomly assign drinkers to other drinkers.


count = 0 
rowCount = 0
print "Inserting..."
for i in range(number):
	#select two drinkers who frequent the same bar
	num = random.randint(0, nFreqs-1)
	bar = freqs[num][1]
	d1 = freqs[num][0]
	d2 = None
	otherNum = num + random.randint(1,3)
	if otherNum < nFreqs:
		if freqs[num][1] == freqs[otherNum][1]:
			#good
			d2 = freqs[otherNum][0]

	if d2 == None:
		#try other direction
		otherNum = num - random.randint(1,3)
		if otherNum >= 0:
			if freqs[num][1] == freqs[otherNum][1]:
				#good
				d2 = freqs[otherNum][0]

	if d2 == None:
		#happens if bar is only frequented by that one person
		continue

	#random date within 1 month
	y = 2013
	m =	10
	d = random.randint(1,31)
	h = int(random.gauss(23, 5)) #this makes a little more sense since people would be leaving a bar at night most likely
	while(h > 23):
		h -= 23 #yup
	while(h < 0):
		h += 23
	mi = random.randint(0,59)

	date = "%04d-%02d-%02d %02d:%02d:00" %(y,m,d,h,mi)
	
	q = "INSERT INTO LeftWith (`drinker1`, `drinker2`, `bar`, `dateOccurred`) VALUES(\"%s\", \"%s\", \"%s\", \"%s\")" % (d1, d2, bar, date)
	#print q
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
#insert them into the leftwith table

cxn.close()

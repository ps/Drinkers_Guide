from settings import *
import datetime
import random
import sys

'''
This file generates Consumed table based first on the drinkers in the LeftWith table. Then the data is generated for rest 

of the drinkers who did not leave with anyone. The second type of drinker visits bars 1-4 times a month. 
'''
cxn = conn_remote()

#get drinkers
print "Fetching leftWith... please wait"
c = cxn.cursor()
c.execute("SELECT * FROM LeftWith")
left = c.fetchall()
c.close()
print len(left)


print "Fetching drinkers... please wait"
c = cxn.cursor()
c.execute("SELECT * FROM Drinker WHERE name NOT IN (SELECT drinker1 FROM LeftWith) AND name NOT IN (SELECT drinker2 FROM LeftWith)")
moreDrinkers = c.fetchall()
c.close()
print len(moreDrinkers)


#used to controll uniqueness of drinkers
usedDrinker = []

#final data to be eneterd into db
consum = []


#i[0] drinker1
#i[1] drinker2
#i[2] dateOccurred
#i[3] bar

print "Generating data based on LeftWith table"
#assign consumed data to those in leftWith table
for info in left:
	if info[0] in usedDrinker:
		continue

	#generate data for drinker 1
	drinker = info[0]
	bar = info[3]
	numDrinks = random.randint(1,4)
	mins = random.randint(1,120)
	newTime =  (info[2])-datetime.timedelta(minutes=mins)

	y = newTime.year 
	m = newTime.month
	d = newTime.day
	h = newTime.hour 
	mi = newTime.minute 

	newTime = "%04d-%02d-%02d %02d:%02d:00" %(y,m,d,h,mi)

	usedDrinker.append(drinker)
	consum.append([drinker,bar,newTime, numDrinks])
	#print [drinker, bar, newTime, numDrinks]


	if info[1] in usedDrinker:
		continue

	#generate date for drinker 2
	drinker2 = info[1]
	numDrinks2 = random.randint(4,10)
	mins2 = random.randint(1,120)
	newTime2 = (info[2])-datetime.timedelta(minutes=mins2)
	
	y= newTime2.year 
	m =newTime2.month
	d = newTime2.day
	h = newTime2.hour
	mi = newTime2.minute 
	newTime2 = "%04d-%02d-%02d %02d:%02d:00" %(y,m,d,h,mi)

	usedDrinker.append(drinker2)
	consum.append([drinker2, bar, newTime2, numDrinks2])
	#print [drinker2, bar, newTime2, numDrinks2]


count = len(moreDrinkers)
print "Generating data based on remaining drinkers in Drinker table"
#assign consumed data to those that do not appear in leftWith
for info in moreDrinkers:
	
	print count
	count = count -1 
	#this shouldn't be the case because of the query but putting it in just in case 
	if info[0] in usedDrinker:
		continue
	usedDrinker.append(info[0])

	visits = random.randint(1,4)
	for k in range(0, visits):
		drinker = info[0]
		drinks = random.randint(1,10)
		#generate date based on 
		y = 2013
		m =	10
		d = random.randint(1,31)
		h = int(random.gauss(23, 5)) #this makes a little more sense since people would be leaving a bar at night most likely
		while(h > 23):
			h -= 23 #yup
		while(h < 0):
			h += 23
		mi = random.randint(0,59)
		dateOfCons = "%04d-%02d-%02d %02d:%02d:00" %(y,m,d,h,mi)

		#get the bars that drinker frequents
		c = cxn.cursor()
		c.execute("""SELECT bar FROM Frequents WHERE drinker=%s""", (drinker))
		freqBars = c.fetchall()
		c.close()
		#print len(freqBars)
		#print freqBars
		pickBar = freqBars[random.randint(0, len(freqBars)-1)]
		pickBar = pickBar[0]
		#print pickBar

		consum.append([drinker,pickBar, dateOfCons, drinks])

print "Inserting into the database..."
for i in consum:
	q = "INSERT INTO Consumed (`drinker`, `bar`, `dateOfConsump`, `numDrinks`) VALUES(\"%s\", \"%s\", \"%s\", \"%s\")" % (i[0], i[1],i[2], i[3])
	
	try:
		cxn.query(q)
	except MySQLdb.Error, e:
		try:
	        	print "MySQL Error [%d]: %s" % (e.args[0], e.args[1])
   		except IndexError:
	        	print "MySQL Error: %s" % str(e)
		print q
		sys.exit()

print "Process completed successfully!"
cxn.close()

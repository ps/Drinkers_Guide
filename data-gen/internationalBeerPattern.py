from settings import *
import random
import sys

'''
This file will take drinkers who frequent international bars but do NOT like a Polish beer and add a Polish beer to the likes table with that drinker so the pattern is satisfied.

SELECT f.drinker FROM Frequents f, Bar b 
WHERE f.bar = b.name AND b.international='1' AND
(SELECT COUNT(*) FROM Likes l WHERE l.drinker = f.drinker AND l.beer IN(
SELECT b.name FROM Beer b, Manufacturer m
WHERE b.manf = m.name AND m.country = 'Poland')) = 0

This pattern states that if a person frequents an international bar, they like a Polish beer
SELECT IF
((SELECT COUNT(f.drinker) FROM Frequents f, Bar b 
WHERE f.bar = b.name AND b.international='1' AND
(SELECT COUNT(*) FROM Likes l WHERE l.drinker = f.drinker AND l.beer IN(
SELECT b.name FROM Beer b, Manufacturer m
WHERE b.manf = m.name AND m.country = 'Poland')) = 0) = 0, 'Yes', 'No') AS verification

'''
cxn = conn_remote()

#get drinkers


print "Fetching people who frequent international bars but do not like Polish beers"
c = cxn.cursor()
c.execute("SELECT f.drinker as name FROM Frequents f, Bar b WHERE f.bar = b.name AND b.international='1' AND (SELECT COUNT(*) FROM Likes l WHERE l.drinker = f.drinker AND l.beer IN(SELECT b.name FROM Beer b, Manufacturer m WHERE b.manf = m.name AND m.country = 'Poland')) = 0")
drinkers = c.fetchall()
c.close()
print "Fetching Polish beers"
c = cxn.cursor()
c.execute("SELECT b.name as name FROM Beer b, Manufacturer m WHERE b.manf = m.name AND m.country = 'Poland'")
pBeers = c.fetchall() #cannot extend pervs1 since fetchall returns tuple, not list
nPBeers = len(pBeers)
c.close()


for d in drinkers:
	randBeer = pBeers[random.randint(0, nPBeers-1)]
	q = "INSERT INTO Likes (`drinker`, `beer`) VALUES(\"%s\", \"%s\")" % (d[0], randBeer[0])
	print q
	try:
		cxn.query(q)
	except MySQLdb.Error, e:
	    try:
	        print "MySQL Error [%d]: %s" % (e.args[0], e.args[1])
	    except IndexError:
	        print "MySQL Error: %s" % str(e)

cxn.close()

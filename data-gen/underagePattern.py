from settings import *
import random
import sys

'''
This pattern states that if an older person left with an underage drinker the older person is a sex offender.

First of all, after running LeftWith, I got too many rows which satisfy this condition, so I deleted some using this query:

Delete From LeftWith WHERE drinker1 in (select name from Drinker WHERE age < 21) LIMIT 150;

After running this is the SQL verification of the pattern:

Logic behind this:
	Drinkers who left with underage drinkers are sex offenders
	Find a drinker who did leave with an underage drinker but is not a sex offender
	I have to check both drinker1 and drinker2 in LeftWith

SELECT CASE WHEN (SELECT COUNT(*) FROM (
	SELECT d1.name
	FROM LeftWith lw, Drinker d1, Drinker d2 
	WHERE lw.drinker1 = d1.name AND d1.age >= 21 AND lw.drinker2 = d2.name and d2.age < 21 AND NOT EXISTS(SELECT * from SexOffender s WHERE s.name = d1.name)
	UNION
	SELECT d1.name
	FROM LeftWith lw, Drinker d1, Drinker d2 
	WHERE lw.drinker2 = d1.name AND d1.age >= 21 AND lw.drinker1 = d2.name and d2.age < 21 AND NOT EXISTS(SELECT * from SexOffender s WHERE s.name = d1.name)
	) A) = 0 THEN 'Yes'
ELSE 'No'
END AS isTrue

'''
cxn = conn_remote()
number = 5000 #number of rows desired

#get drinkers


print "Fetching perverts (part 1)... please wait"
c = cxn.cursor()
c.execute("SELECT lw.drinker1 AS offender, lw.drinker2 as victim, lw.dateOccurred FROM LeftWith lw, Drinker d, Drinker d2 WHERE d.age < 21 AND d.name = lw.drinker2 AND d2.name = lw.drinker1 and d2.age >= 21;")
pervs1 = c.fetchall()
c.close()
print "Fetching perverts (part 2)... please wait"
c = cxn.cursor()
c.execute("SELECT lw.drinker2 AS offender, lw.drinker1 AS victim, lw.dateOccurred FROM LeftWith lw, Drinker d, Drinker d2 WHERE d.age < 21 AND d.name = lw.drinker1 AND d2.name = lw.drinker2 and d2.age >= 21;")
pervs2 = c.fetchall() #cannot extend pervs1 since fetchall returns tuple, not list
c.close()
#randomly assign drinkers to other drinkers.
print "TOTAL %d" % (len(pervs1) + len(pervs2))



print "Inserting the pervs..."

def insertPervs(pList):
	count = 0 
	for p in pList:
		dt = "%4d-%02d-%02d" %(p[2].year, p[2].month, p[2].day)
		q = "INSERT INTO SexOffender (`name`, `victim`, `dateOfCrime`) VALUES(\"%s\", \"%s\", \"%s\")" % (p[0], p[1], dt)
		
		
		print "Done %d " % count
		count += 1
		try:
			cxn.query(q)
		except MySQLdb.Error, e:
		    try:
		        print "MySQL Error [%d]: %s" % (e.args[0], e.args[1])
		    except IndexError:
		        print "MySQL Error: %s" % str(e)

#insert them into the leftwith table
insertPervs(pervs1)
print "Part 1 done"
insertPervs(pervs2)
print "Part 2 done"
cxn.close()

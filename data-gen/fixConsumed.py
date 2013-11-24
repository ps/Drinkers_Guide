import MySQLdb
import re
import random
import sys 
'''
The script fixed the consumed table where certain entries had 0 in the numDrinks field and assigns a number 1-10 into that field.
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
cxn.close()

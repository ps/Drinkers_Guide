import random
import MySQLdb
import sys
from settings import *
'''
This script pulls distinct alcohol content and generates prices that increase as alcohol content increases.
'''

cxn = conn_remote()

#get alcohol content 
print "Fetching alcohol content..."
c = cxn.cursor()
c.execute("SELECT DISTINCT alcContent FROM Beer ORDER BY alcContent")
alc = c.fetchall()
c.close()

startPrice = 1.5

for i in alc:
	difference = random.randint(1,20)
	add = difference * 0.01
	outPrice = startPrice + add;
	print "Alc: %s\tPrice: %.2f" % (i[0], outPrice)
	startPrice = outPrice

	exportPrice = "%.2f" % (outPrice)
	
	q = """UPDATE Sells set price=%s WHERE beer IN (SELECT name FROM Beer WHERE ABS(alcContent-%s)<=0.01)"""
	c = cxn.cursor()
	try:
		rowsAffected = c.execute(q,(exportPrice, i[0]))
		print rowsAffected
	except MySQLdb.Error, e:
		print e
		break
	cxn.commit()
	c.close()

cxn.close()

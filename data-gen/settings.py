import MySQLdb

datadir = "data/"
def conn_remote():
	db = MySQLdb.connect(host="cs336-9.cs.rutgers.edu", user="root", passwd="root", db="SAFETY_DB")
	print "Connection made"
	return db

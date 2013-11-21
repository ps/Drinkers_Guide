# -*- coding: utf-8 -*-A
'''
What this does right now is just print out the data from beerlist.php!

The way I ran this was:
1. inserted the beers by uncommenting the needed insertion/error sections
2. commented out beers insertion/error code and uncommented country insertion/error code
3. commented out country insertion/error code and uncommented manufacturer insertion/error code

'''
import urllib2
import MySQLdb
import random
import sys
import unicodedata
from bs4 import BeautifulSoup

#sanitize from unicode characters to ascii
def sanitizeString(str):
	str = unicodedata.normalize('NFKD', str).encode('ascii', 'ignore')
	return str

#connect to db
print "Connectin to db"
db = MySQLdb.connect(host="cs336-9.cs.rutgers.edu", user="root", passwd="root", db="SAFETY_DB")
cursor = db.cursor()

print "Getting data"
#soup = BeautifulSoup(urllib2.urlopen('http://www.beerme.com/beerlist.php'))
soup = BeautifulSoup(open("data/list_of_beers.html"))
print "Got page"

print "Finding table"
rows = soup.find("table", {"class":"beerlist"}).findAll("tr")
print "Got table, time to print\n\n"

#unique countries
uc = []

#unique manf with country
um = []

num=0

#stores errors for beer db
errors = []

#stores errors for country db
cerrors = []

#stores errors for manufacturer db
merrors = []

#in each cols variable
#---------------------
#0 is brewery/beer
#1 is style
#2 is location
#3 is catalog
#4 is score
#5 is date

rem = len(rows)
for row in rows:
	print rem
	rem = rem - 1
	cols = row.findAll("td")
	out = ""
	colnum=0

	beer = cols[0].findAll("a")
	beer = beer[0].contents[0]
	manf = cols[0].contents[0]
	style = cols[1].contents[0]
	location = cols[2].contents[0]
	alcCont = (random.randint(10,100)) * 0.1

	#sanitize from unicode characters
	beer = sanitizeString(beer)
	manf = sanitizeString(manf)
	style = sanitizeString(style)

	#sanitize for database
	'''
	try:
		beer = MySQLdb.escape_string(beer)
	except:
		print "Error"
		print beer
		sys.exit()
	'''
	try:
		manf = MySQLdb.escape_string(manf)
	except:
		print "Error"
		print manf
		sys.exit()
	try:
		style = MySQLdb.escape_string(style)
	except:
		print "Error"
		print style
		sys.exit()
	

	#some beers had county/city so i had to get rid of that
	country = ""
	town = "---"
	if "-" in location:
		subloc = location.split("-")
		country = subloc[0]
		town = subloc[1]
	if not country:
		country = location
	
	if country not in uc:
		uc.append(country)
	
	if [manf,country] not in um:
		um.append([manf,country])

	#sanitize location
	location = sanitizeString(location)
	try:
		location = MySQLdb.escape_string(location)
	except:
		print location
		sys.exit()


	#use the commented code below to insert beers into db
	
	query = """INSERT INTO Beer (name, alcContent,style,manf) VALUES (%s,%s, %s, %s) """
	
	try:
		cursor.execute(query, (beer,alcCont, style,manf))
	except MySQLdb.Error, e:
		errors.append([e,beer,alcCont,style,manf])
		print beer
		print e
	db.commit()
	
'''
	#print the data
	print "Beer #%i: %s" % (num,beer)
	print "Manf: %s" % (manf)
	print "Style: %s" % (style)
	print "Country %s" % (country)
	print "Town: %s\n" % (town)
	
	num = num +1
'''
	

#print beer errors
'''
print "\n\n\nErrors"
for err in errors:
	print err[0]

print "Done..."
'''


#insert countries into db
'''
print "Inserting countries"
for country in uc:
	print country
	query = """INSERT INTO Country (name) VALUES (%s) """
	try:
		cursor.execute(query, (country))
	except MySQLdb.Error, e:
		cerrors.append([e,country])
	db.commit()
'''

#print errors for countries insertion
'''
for err in cerrors:
	print err[0]
print "DONE!"
'''


#insert manufacturer and country into db
'''
print "Startin insert of manufacturer"
for man in um:
	print "manf: %s country: %s" % (man[0], man[1])

	query = """INSERT INTO Manufacturer (name,country) VALUES (%s,%s) """
	try:
		cursor.execute(query, (man[0],man[1]))
	except MySQLdb.Error, e:
		merrors.append([e,man[0],man[1]])
	db.commit()
'''

#print errors for manufacturer and country insertion
'''
for err in merrors:
	print err[0]
print "DONE MAN!"
'''

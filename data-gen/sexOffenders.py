from settings import *
import random
'''
This file generates the SexOffender table based on the data already in the Drinker table

There are 220 male sex offenders (210 assult women, 10 assult men)
There are 20 female sex offenders (15 assult men, 5 assult women)
Out of male vicitms, 10 become sex offenders and assult women
Out of female victims, 2 become sex offenders and assult men 

Total: 252 sex offenders 

Each offender commits 1-2 offenses in the month of October

80% of offenses occured on the weekend [Thursday, Friday, Saturday]
'''
cxn = conn_remote()

#get drinkers
print "Fetching drinkers... please wait"
c = cxn.cursor()
c.execute("SELECT * FROM Drinker WHERE gender='M'")
maleDrinkers = c.fetchall()
c.close()

c = cxn.cursor()
c.execute("SELECT * FROM Drinker WHERE gender='F'")
femDrinkers = c.fetchall()
c.close()

c = cxn.cursor()
c.execute("SELECT * FROM Drinker")
drinkers = c.fetchall()
c.close()

finalOffenderPairs = []

print "Making matches"

#list of all male/female victims used 
overAllMenVic = []
overAllFemVic = []

#get 220 man sex offenders (210 offended women, 10 offended man)
menCriminals = []
for i in range (0,220):
	offender = maleDrinkers[random.randint(0,len(maleDrinkers)-1)]
	while True:
		if offender[0] in menCriminals:
			offender = maleDrinkers[random.randint(0,len(maleDrinkers)-1)]
		else:
			break
	#print offender[0]
	menCriminals.append(offender[0])

#get 20 women sex offenders (15 offended men, 5 offended women)
femCriminals = []
for i in range (0,20):
	offender = femDrinkers[random.randint(0,len(femDrinkers)-1)]
	while True:
		if offender[0] in femCriminals:
			offender = femDrinkers[random.randint(0,len(femDrinkers)-1)]
		else:
			break
	#print offender[0]
	femCriminals.append(offender[0])

#get 10 men victims for menOnMen
menVictims = []
for i in range (0,10):
	vic = maleDrinkers[random.randint(0, len(maleDrinkers)-1)]
	while True:
		if (vic[0] in menVictims) or (vic[0] in menCriminals):
			vic = maleDrinkers[random.randint(0, len(maleDrinkers)-1)]
		else:
			break
	menVictims.append(vic[0])
	overAllMenVic.append(vic[0])

#get 5 women victims for womenOnWomen
femVictims = []
for i in range (0,5):
	vic = femDrinkers[random.randint(0, len(femDrinkers)-1)]
	while True:
		if (vic[0] in femVictims) or (vic[0] in femCriminals):
			vic = femDrinkers[random.randint(0, len(femDrinkers)-1)]
		else:
			break
	femVictims.append(vic[0])
	overAllFemVic.append(vic[0])

#for each man sex offender assign 1-2 offenses
menOnMen = 10
for i in menCriminals:
	numberOfOffenses = random.randint(1,2)
	for k in range(0,numberOfOffenses):
		if menOnMen >0:
			finalOffenderPairs.append([i,menVictims[menOnMen-1]])
			menOnMen = menOnMen -1
			
		else:
			vic = femDrinkers[random.randint(0,len(femDrinkers)-1)]
			while True:
				if (vic[0] in femVictims) or (vic[0] in femCriminals) or (vic[0] in overAllFemVic):
					vic = femDrinkers[random.randint(0,len(femDrinkers)-1)]
				else:
					break
			finalOffenderPairs.append([i,vic[0]])
			overAllFemVic.append(vic[0])

#for each women sex offender assign 1-2 offenses
femOnFem = 5
for i in femCriminals:
	numberOfOffenses = random.randint(1,2)
	for k in range(0,numberOfOffenses):
		if femOnFem >0:
			finalOffenderPairs.append([i,femVictims[femOnFem-1]])
			femOnFem = femOnFem -1
			
		else:
			vic = maleDrinkers[random.randint(0,len(maleDrinkers)-1)]
			while True:
				if (vic[0] in menVictims) or (vic[0] in menCriminals) or (vic[0] in overAllMenVic):
					vic = maleDrinkers[random.randint(0,len(maleDrinkers)-1)]
				else:
					break
			finalOffenderPairs.append([i,vic[0]])
			overAllMenVic.append(vic[0])


#pick 10 man victims and make them sex offenders
for i in range(0,10):
	numberOfOffenses = random.randint(1,2)
	for k in range(0, numberOfOffenses):
		vic = femDrinkers[random.randint(0,len(femDrinkers)-1)]
		while True:
			if (vic[0] in femVictims) or (vic[0] in femCriminals) or (vic[0] in overAllFemVic):
				vic = femDrinkers[random.randint(0,len(femDrinkers)-1)]
			else:
				break
		finalOffenderPairs.append([menVictims[i],vic[0]])
		overAllFemVic.append(vic[0])

#pick 2 women victims and make them sex offenders
for i in range(0,2):
	numberOfOffenses = random.randint(1,2)
	for k in range(0, numberOfOffenses):
		vic = maleDrinkers[random.randint(0,len(maleDrinkers)-1)]
		while True:
			if (vic[0] in menVictims) or (vic[0] in menCriminals) or (vic[0] in overAllMenVic):
				vic = maleDrinkers[random.randint(0,len(femDrinkers)-1)]
			else:
				break
		finalOffenderPairs.append([femVictims[i],vic[0]])
		overAllMenVic.append(vic[0])


pairsWithDates = []
#used criminal-date combination to make sure the same criminal does not 
#commit two offenses on the same day
crimDateComb = []

#80% of offenses occured on weekend (thursday, friday, saturday)
weekend = len(finalOffenderPairs) * 0.8

#assign dates to crimes 
for i in finalOffenderPairs:
	day = random.randint(1,32)
	while True:
		if weekend >0:
			if( (day>2 and day <6) or (day>9 and day<13) or (day>16 and day<20) or (day>23 and day<27) or day==31):
				weekend = weekend -1
				break
			else:
				day = random.randint(1,32)
		else:
			if( (day>2 and day <6) or (day>9 and day<13) or (day>16 and day<20) or (day>23 and day<27) or day==31 ):
				day = random.randint(1,32)
			else:
				break

	while True:
		if [i[0], day] in crimDateComb:
			day = random.randint(1,32)
			while True:
				if weekend >0:
					if( (day>2 and day <6) or (day>9 and day<13) or (day>16 and day<20) or (day>23 and day<27) or day==31):
						weekend = weekend -1
						break
					else:
						day = random.randint(1,32)
				else:
					if( (day>2 and day <6) or (day>9 and day<13) or (day>16 and day<20) or (day>23 and day<27) or day==31 ):
						day = random.randint(1,32)
					else:
						break
		else:
			break
	crimDateComb.append([i[0], day])
	pairsWithDates.append([i[0],i[1],day])

errors = []
print "Inserting into the database..."
for i in pairsWithDates:
	#print i
	dateOfCrime = "2013-10-%i" % (i[2])
	q = """INSERT INTO SexOffender (name, dateOfCrime, victim) VALUES(%s,%s,%s)"""
	c = cxn.cursor()
	try:
		c.execute(q,(i[0],dateOfCrime,i[1]))
	except MySQLdb.Error, e:
		print e
		errors.append([e,i[0],dateOfCrime, i[1]])
		break
	cxn.commit()
	c.close()

for i in errors:
	print i

'''
#just a count to verify that there are 252 unique predators 
unique = []

#debuggin array to see how many offenses occured on each day 
dayCount = [0]*40
for i in pairsWithDates:
	dayCount[i[2]] = dayCount[i[2]]+1
	if i[0] not in unique:
		unique.append(i[0])

for i in range(1,32):
	print "Day:%i\tCount:%i" % (i,dayCount[i])

	#print "Criminal: %s\t\tVictim: %s" % (i[0],i[1])
print len(pairsWithDates)
print len(finalOffenderPairs)
print len(unique)
'''

cxn.close()

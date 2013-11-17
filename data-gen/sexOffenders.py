from settings import *
import random
'''
This generation file assumes that the database already has the drinkers as well as the beers already in place.

Also, this does not check for duplicate entries, but relies on the SQL primary key restraints to prevent duplicates.
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


overAllVictims = []

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

#for each man sex offender assign 1-3 offenses
menOnMen = 10
for i in menCriminals:
	numberOfOffenses = random.randint(1,4)
	for k in range(0,numberOfOffenses):
		if menOnMen >0:
			finalOffenderPairs.append([i,menVictims[menOnMen-1]])
			menOnMen = menOnMen -1
			
		else:
			vic = femDrinkers[random.randint(0,len(femDrinkers)-1)]
			while True:
				if (vic[0] in femVictims) or (vic[0] in femCriminals):
					vic = femDrinkers[random.randint(0,len(femDrinkers)-1)]
				else:
					break
			finalOffenderPairs.append([i,vic[0]])
			overAllVictims.append(vic[0])

#for each women sex offender assign 1-3 offenses
femOnFem = 5
for i in femCriminals:
	numberOfOffenses = random.randint(1,4)
	for k in range(0,numberOfOffenses):
		if femOnFem >0:
			finalOffenderPairs.append([i,femVictims[femOnFem-1]])
			femOnFem = femOnFem -1
			
		else:
			vic = maleDrinkers[random.randint(0,len(maleDrinkers)-1)]
			while True:
				if (vic[0] in menVictims) or (vic[0] in menCriminals):
					vic = maleDrinkers[random.randint(0,len(maleDrinkers)-1)]
				else:
					break
			finalOffenderPairs.append([i,vic[0]])
			overAllVictims.append(vic[0])


#pick 10 man victims and make them sex offenders
for i in range(0,10):
	numberOfOffenses = random.randint(1,4)
	for k in range(0, numberOfOffenses):
		vic = femDrinkers[random.randint(0,len(femDrinkers)-1)]
		if (vic[0] in femVictims) or (vic[0] in femCriminals) or (vic[0] in overAllVictims):
			vic = femDrinkers[random.randint(0,len(femDrinkers)-1)]
		else:
			break
		finalOffenderPairs.append([menVictims[i],vic[0]])

#pick 2 women victims and make them sex offenders
for i in range(0,2):
	numberOfOffenses = random.randint(1,4)
	for k in range(0, numberOfOffenses):
		vic = maleDrinkers[random.randint(0,len(maleDrinkers)-1)]
		if (vic[0] in menVictims) or (vic[0] in menCriminals) or (vic[0] in overAllVictims):
			vic = maleDrinkers[random.randint(0,len(femDrinkers)-1)]
		else:
			break
		finalOffenderPairs.append([femVictims[i],vic[0]])


for i in finalOffenderPairs:
	print "Criminal: %s\t\tVictim: %s" % (i[0],i[1])
print len(finalOffenderPairs)

print "Sex offenders generated now assign dates"


cxn.close()

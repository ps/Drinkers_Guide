import random
import string
import cities
from settings import *

#makes about 60,000 bars

#press ctrl-c to stop, or wait

part1 = ["Angry ","Berserk ","Black ","Blind ","Bloody ","Blue ","Brass ","Broken ","Cheerfull ","Crimson ","Dancing ","Dark ","Dead ","Death's ","Dirty ","Doomed ","Drunken ","Fiery ","Flying ","Golden ","Growling ","Howling ","Hungry ","Iron ","Jovial ","Joyfull ","Laughing ","Leaping ","Little ","Lone ","Mighty ","Murky ","Mystic ","Nine ","Old ","Olden ","Pale ","Rouge ","Ruddy ","Running ","Savage ","Scarlett ","Screaming ","Seven ","Silver ","Sleeping ","Small ","Staggering ","Stone ","Toothless ","Twin ","White ","Wild ","Winking ",""]

part2 = ["Ale ","Angel ","Archer ","Arms ","Balrog ","Bandit ","Bard ","Barrel ","Battle Axe ","Begger ","Bottle ","Centaur ","Champion ","Crossroads ","Cyclops ","Dagger ","Demon ","Dragon ","Dwarf ","Fist ","Flame ","Flask ","Flood ","Fox ","Gargoyle ","Gazelle ","Giant ","Goblet ","Goblin ","Godling ","Griphin ","Grog ","Hand ","Harpy ","Helm ","Heretic ","Hippogriff ","Hunter ","Kelpie ","Knave ","Knight ","Knuckle ","Lance ","Lion ","Litch ","Lizard ","Minotaur ","Naga ","Nobleman ","Nymph ","Ogre ","Orc ","Prophet ","Rat ","Rouge ","Sage ","Scepter ","Scribe ","Spirits ","Squire ","Sword ","Tankard ","Titan ","Traveller ","Troll ","Unicorn ","Vagabond ","Vixen ","Wanderer ","Warrior ","Wench ","Wheel ","Wind ","Wolf "]

part3 = ["Alehouse ","Cellar ","Clubhouse ","Guesthouse ","House ","Inn ","Lodge ","Meadhall ","Resthouse ","Tavern ","Hall ","Grogshop ","Taproom ","Barroom "]

polish = ["Cieknacy But","Flaszka u Leszka","Gruba Rura","Donald I Przyjaciele","Pod Myszka" ,"Jeszcze Jeden","Budka Suflera","Wehikul Czasu","Wlaz Kotek na Plotek","Pod Sokolem","Wieczny Bal","Trzezwy Student","Kapitan Bomba","Nowicjusz","Maluszek","Brat Robert","Tereska","Bar Doda","Atlas","Pudzian","Zbysiu","Bar Jasionka","Same Przyjemnosci","Jurek Ogorek","Narcyz","Pod Kopytkiem","Prawie Jak Ameryka","Bar Abstynentow","Wierny Rys","Dupa Biskupa","Zloty Rak","Czarna Magia","Chatka Puchatka"
]



def justPolish():
	global polish
	outFile = open(datadir + "bars.sql", "w")
	outFile.write("INSERT INTO Bar (`name`, `phone`, `city`, `state`, `latitude`, `longitude`, `license`, `international`) VALUES ")
	first = True
	for i in polish:
		name = i
		license = ''.join(random.choice(string.ascii_uppercase + string.digits) for x in range(10))#stolen from stackoverflow
		randPlace = cities.getCity()
		city = randPlace[0]
		state = randPlace[1]
		lat = randPlace[2]
		lon = randPlace[3]
		randPhone = "%i-%i-%i" % (random.randint(100,999),random.randint(100,999), random.randint(1000,9999))
		if not first:
			outFile.write(",")
		else:
			first = False
		outFile.write("(\"%s\", \"%s\", \"%s\", \"%s\", %f, %f, \"%s\", 1)" % (name, randPhone, city, state, lat, lon, license))
		outFile.write("\n")
	outFile.close()
	print("Done -- output in " + datadir + "bars.sql")

def everythingElse():
	global polish
	outFile = open(datadir + "bars.sql", "w")
	outFile.write("INSERT INTO Bar (`name`, `phone`, `city`, `state`, `latitude`, `longitude`, `license`) VALUES ")
	first = True
	for i in part1:
		for j in part2:
			for k in part3:
				name = i + j + k #calc3
				license = ''.join(random.choice(string.ascii_uppercase + string.digits) for x in range(10))#stolen from stackoverflow
				randPlace = cities.getCity()
				city = randPlace[0]
				state = randPlace[1]
				lat = randPlace[2]
				lon = randPlace[3]
				randPhone = "%i-%i-%i" % (random.randint(100,999),random.randint(100,999), random.randint(1000,9999))
				if not first:
					outFile.write(",")
				else:
					first = False
				outFile.write("(\"%s\", \"%s\", \"%s\", \"%s\", %f, %f, \"%s\")" % (name, randPhone, city, state, lat, lon, license))
				outFile.write("\n")
	outFile.close()
	print("Done -- output in " + datadir + "bars.sql")

justPolish()
import random
import string
import cities
from settings import *

#makes about 60,000 bars

#press ctrl-c to stop, or wait

part1 = ["Angry ","Berserk ","Black ","Blind ","Bloody ","Blue ","Brass ","Broken ","Cheerfull ","Crimson ","Dancing ","Dark ","Dead ","Death's ","Dirty ","Doomed ","Drunken ","Fiery ","Flying ","Golden ","Growling ","Howling ","Hungry ","Iron ","Jovial ","Joyfull ","Laughing ","Leaping ","Little ","Lone ","Mighty ","Murky ","Mystic ","Nine ","Old ","Olden ","Pale ","Rouge ","Ruddy ","Running ","Savage ","Scarlett ","Screaming ","Seven ","Silver ","Sleeping ","Small ","Staggering ","Stone ","Toothless ","Twin ","White ","Wild ","Winking ",""]

part2 = ["Ale ","Angel ","Archer ","Arms ","Balrog ","Bandit ","Bard ","Barrel ","Battle Axe ","Begger ","Bottle ","Centaur ","Champion ","Crossroads ","Cyclops ","Dagger ","Demon ","Dragon ","Dwarf ","Fist ","Flame ","Flask ","Flood ","Fox ","Gargoyle ","Gazelle ","Giant ","Goblet ","Goblin ","Godling ","Griphin ","Grog ","Hand ","Harpy ","Helm ","Heretic ","Hippogriff ","Hunter ","Kelpie ","Knave ","Knight ","Knuckle ","Lance ","Lion ","Litch ","Lizard ","Minotaur ","Naga ","Nobleman ","Nymph ","Ogre ","Orc ","Prophet ","Rat ","Rouge ","Sage ","Scepter ","Scribe ","Spirits ","Squire ","Sword ","Tankard ","Titan ","Traveller ","Troll ","Unicorn ","Vagabond ","Vixen ","Wanderer ","Warrior ","Wench ","Wheel ","Wind ","Wolf "]

part3 = ["Alehouse ","Cellar ","Clubhouse ","Guesthouse ","House ","Inn ","Lodge ","Meadhall ","Resthouse ","Tavern ","Hall ","Grogshop ","Taproom ","Barroom "]


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
			print "%s, %s, %s [%0.3f, %0.3f] License: %s" % (name, city, state, lat, lon, license)

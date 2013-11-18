CS336 Databases Project Bitch
=============================

## Notes ##

Using [this post](http://stackoverflow.com/questions/8380797/enable-remote-mysql-connection) I was able to get remote connection allowed so we do not have to SSH before connecting. This way we can just run the scripts on our machines and have MySQLDB connect remotely. However, stupid me made the password 'root' instead of the other password. 

Hostname: <b>cs336-9.cs.rutgers.edu</b>

Username: <b>csuser</b>

Password: <b>csad1a0d</b>

To import large files into the database:
http://stackoverflow.com/questions/93128/mysql-error-1153-got-a-packet-bigger-than-max-allowed-packet-bytes

In MySQL shell use these lines to increase the max packet size
```
set global net_buffer_length=1000000; 
set global max_allowed_packet=1000000000;
```
Then put this in the background with Ctrl-Z. Do not exit this shell until after importing

* server directory is at /usr/local/httpd-2.2.14/htdocs [this is so gay]
* mysql passwd same as username passwd
* mapped 'main' to put you in the public html directory [only under root though]
* server has python version 2.4.3 so had to use BeautifulSoup version 3...
* original large sqldump is in file dump.sql


In the SexOffender table, two people have date of 0000-00-00, not sure if we should delete them or add a date. [taken care of]
```
select * from SexOffender where dateOfCrime='0000-00-00';
```

#### We have decided to largely cut down on the amount of data ####
The data is now for one month of October 2013 with the following assumptions:
- ~5k drinkers
- ~3k bars 
- ~250 sex offenders 
- Each drinker goes to bar on average 3-4 times 
- Each drinker frequents 2-3 different bars 

## Ideas ##
Some of these are difficult to implement but who knows.

* Use the data to determine a safety ranking of bars based off of the number of offences commited at a bar
* Visualize this with a map (not necessarily google maps), if we stick to one state, we could roll our own simple map which just plots each bar and has a heatmap of how dangerous the bars are
* Have a form to report offences
* Have a tool which locates the safest bar in your area to go to
* If we want to do something with the location, should we have real city/town names with their legitimate zip codes etc? We can get a list of them from [this wikipedia page](http://en.wikipedia.org/wiki/List_of_municipalities_in_New_Jersey)
* Right now I favored Polish bars and beers. However, the data might visualize better if I don't favor Polish bars, just beers, so the distribution of people is still genuine to the population.

## Time Table ##
This week:
- Actually create the tables from our flowchart
- Finish all data generation 
- Enforce all of the patterns we want

Next week:
- Make the backend (probably in PHP)
- Make forms to report offences, etc.
- Make at least a couple visualizations of most dangerous areas
- Add profiles of sex offenders
- Real town names from NJ, NY, PA
- Possibly use [google heatmap](https://developers.google.com/maps/documentation/javascript/examples/layer-heatmap)



## Data Generation ##

### Name generation ###
- Run simply with: python drinkers.py (BeautifulSoup needs to be installed, internet connection needed)
- First and last names are being pulled from the respective text/csv files
- Gender is provided with the names (hard coded to make 20% of names female)
- Age and phone number generated with the random class
- Street name is generated by posting the first name to the Wu-Tang Name Generator (http://www.mess.be/inickgenwuname.php) and scraping the result
- Street number and type (Ave/St/Cir) generated randomly

### Other tables ###
- Towns (for Drinker and Bar) [Kevin - done 11/13]
	- Use wikipedia lists, save to text files. Use google maps api to get lat/lon before generating the other stuff. Save to the text files.
- Drinker(name, address, phone, gender, latitude, longitude, state, city, age) - mostly done, just add lat/lon/town [Kevin - done 11/13]
- Bar(name, address, license, phone, latitude, longitude, state, city) [Kevin - done 11/13]
- Sells(bar, beer, price) [Kevin]
- Beer(name, alcCont, manf) [Pawel - done 11/13]
- SexOffender(name, dateOccured, sexOffendee) [Pawel - done 11/17] - add pattern for past sex offendees to be more likely to be sex offenders, make sexoffendee other gender most of the time
- Frequents(drinker, bar) [Kevin - done 11/16, just need to add primary key]
- Likes(drinker, beer) [Kevin]
- LeftWith(drinker1, drinker2, dateOccurred, bar) [Kevin]
	- this will match up with frequents
	- this should probably also match up with Consumed and SexOffender (date occurred).
	- also need to implement pattern (will do afterwards, that if one person is underage then the other is a sexoffender)
- Manufacturer(name, country) [Pawel - done 11/13]
- Country(name, isAlcProhib) [Pawel - done 11/13]
- Consumed(drinker, bar, date, numDrinks) [Pawel - done 11/17]

### Patterns ###

We need to add a couple of patterns to the data. It might be easier to add all of the data initially and then implement the patterns by deleting records which go against the pattern. This way we can just worry about making a shit ton of data for now.

#### People who leave with underage drinkers are sex offenders ####

This pattern states that if an older person left with an underage drinker the older person is a sex offender.

After running this is the SQL verification of the pattern:

Logic behind this:
	Drinkers who left with underage drinkers are sex offenders
	Find a drinker who did leave with an underage drinker but is not a sex offender
	I have to check both drinker1 and drinker2 in LeftWith
```sql
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
```

#### Bars which serve illegal beers have sex offenders ####

Here is the verification of the pattern:

```sql
SELECT CASE WHEN 
(SELECT COUNT(*) 
FROM Sells s 
WHERE s.beer IN (SELECT name FROM Beer where manf in (SELECT m.name FROM Manufacturer m,Country c Where m.country = c.name AND prohibition=1))
AND NOT EXISTS (SELECT * FROM SexOffender WHERE bar = s.bar)) = 0 THEN 'Yes'
ELSE 'No'
END AS isTrue
```

#### Safety Ranking ####

Ok, so this query gets the number of sex offenders all of the bars have:
```sql
SELECT b.name, COUNT(A.name) AS cnt 
FROM Bar b LEFT JOIN (SELECT f.bar AS bar, s.name AS name FROM SexOffender s, Frequents f WHERE s.name = f.drinker) A ON b.name = A.bar GROUP BY b.name ORDER By cnt;
```
Maybe we can use this some way to generate the rankings with SQL. Otherwise we can use PHP.

Some possibilities:
- bars with sex offenders have underage drinkers [done 11/17]
- bars with illegal beers have sex offenders [done 11/17]
- the higher the alchohol content in the beers which a bar serves correlates to how many sex offenders frequent that bar
- everybody who frequents an international bar likes Zywiec (we could add a trigger to make sure anyone added into Frequents with an international bar has a corresponding row in the Likes table)
- bars which serve beers with higher alchohol content are less safe
- generate a safety rating for a bar (possibly using the time too)
	- Our ranking can be on a 1-10 scale:
	 	+ 8-10 the bar is safe. There is low risk of visiting this bar
	 	+ 5-7 There is some risk.
	 	+ 3-4 There is a moderate risk.
	 	+ 1-2 There is high risk.
<?php 
	require_once("settings.php");
	$PG_NAME = "technical";
	require_once("res/top.php"); //header, navigation, opening tags etc.
?>
<script src="res/shCore.js"></script>
<script src="res/shBrushSql.js"></script>
<h3 data-link="target" class="expand-link">Target Audience and Business Aspects <span>expand</span></h3>
<div data-box="target" class="expand-box">
	<p>Target Audience: Drinkers and Police</p>
	<h4>Drinkers</h4>
	<ul>
		<li>Find safe bar around them</li>
		<li>Be aware which predators go to the same bars that they do</li>
	</ul>
	<h4>Police</h4>
	<ul>
		<li>Find bars most likely to have sex offenders</li>
		<li>Use prediction to find when sex offenders are going to strike next</li>

	</ul>
</div>

<h3 data-link="patterns" class="expand-link">Patterns <span>expand</span></h3>
	<div data-box="patterns" class="expand-box">
	<p>Our patterns were placed after our data was generated. The files which enforce the patterns are listed next the the pattern names below</p>
	

	<h4>The higher the alcohol content of the beer the higher the price (beer_price.py)</h4>

	<pre class="brush: sql">
	SELECT CASE WHEN (SELECT COUNT(*) FROM (
		SELECT A.beer FROM 
		(SELECT * FROM Sells B, Beer C WHERE B.beer=C.name) A, 
		(SELECT * FROM Sells B, Beer C WHERE B.beer=C.name) D 
		WHERE (ABS(A.alcContent-D.alcContent)&lt;=0.01 OR A.alcContent > D.alcContent) 
		AND A.beer&lt;>D.beer 
		AND A.price &lt; D.price
    ) A) = 0 THEN 'Yes'
	ELSE 'No'
	END AS isTrue
	</pre>
	<div class="veri">
		<button class="verify" data-which="alc">Verify this pattern</button><img class="loader" src="res/loader.gif" /><span></span>
	</div>


	<h4>People who leave with underage drinkers are sex offenders (underagePattern.py)</h4>

	<p>This pattern states that if an older person left with an underage drinker the older person is a sex offender.</p>
	<pre class="brush: sql">
	SELECT CASE WHEN (SELECT COUNT(*) FROM (
		SELECT d1.name
		FROM LeftWith lw, Drinker d1, Drinker d2 
		WHERE lw.drinker1 = d1.name AND d1.age >= 21 AND lw.drinker2 = d2.name and d2.age &lt; 21 AND NOT EXISTS(SELECT * from SexOffender s WHERE s.name = d1.name)
		UNION
		SELECT d1.name
		FROM LeftWith lw, Drinker d1, Drinker d2 
		WHERE lw.drinker2 = d1.name AND d1.age >= 21 AND lw.drinker1 = d2.name and d2.age &lt; 21 AND NOT EXISTS(SELECT * from SexOffender s WHERE s.name = d1.name)
		) A) = 0 THEN 'Yes'
	ELSE 'No'
	END AS isTrue
	</pre>
	<div class="veri">
		<button class="verify" data-which="underage">Verify this pattern</button><img class="loader" src="res/loader.gif" /><span></span>
	</div>

	<h4>Bars which serve illegal beers have sex offenders (illegalBeerPattern.py)</h4>
	<p>
		Here is the verification of the pattern:
	</p>
	<pre class="brush: sql">
	SELECT CASE WHEN 
	(SELECT COUNT(*) 
	FROM Sells s 
	WHERE s.beer IN (SELECT name FROM Beer where manf in (SELECT m.name FROM Manufacturer m,Country c Where m.country = c.name AND prohibition=1))
	AND NOT EXISTS (SELECT * FROM SexOffender WHERE bar = s.bar)) = 0 THEN 'Yes'
	ELSE 'No'
	END AS isTrue
	</pre>
	<div class="veri">
		<button class="verify" data-which="offender">Verify this pattern</button><img class="loader" src="res/loader.gif" /><span></span>
	</div>

	<h4>People who frequent international bars like at least one Polish beer (internationalBeerPattern.py)</h4>
	<p>Here is a verification of the pattern</p>
	<pre class="brush: sql">
	SELECT IF
	((SELECT COUNT(f.drinker) FROM Frequents f, Bar b 
	WHERE f.bar = b.name AND b.international='1' AND
	(SELECT COUNT(*) FROM Likes l WHERE l.drinker = f.drinker AND l.beer IN(
	SELECT b.name FROM Beer b, Manufacturer m
	WHERE b.manf = m.name AND m.country = 'Poland')) = 0) = 0, 'Yes', 'No') AS isTrue
	</pre>
	<div class="veri">
		<button class="verify" data-which="polish">Verify this pattern</button><img class="loader" src="res/loader.gif" /><span></span>
	</div>
</div>

<h3 data-link="queries" class="expand-link">Queries Behind the Scenes <span>expand</span></h3>
<div data-box="queries" class="expand-box">
	<h4>Bar Safety Ratings (bar.php)</h4>
	<p>
		This query selects the number of illegal beers sold and sex offenders/victims who frequent the bar, 
		and converts it to a 0-10 scale. 
	</p>
	<pre class="brush: sql">
	SELECT b.city AS city, b.name AS name,
    ROUND((10 - (COUNT(A.name) +
        (SELECT COUNT(*)  FROM Sells s  WHERE s.bar = b.name AND s.beer IN
            (SELECT name FROM Beer WHERE manf IN
                (SELECT m.name FROM Manufacturer m,Country c WHERE
                m.country = c.name AND prohibition=1)))) * (10/13)),1) AS rating
	FROM Bar b LEFT JOIN
    (SELECT c.bar AS bar, s.name AS name FROM SexOffender s, Frequents c
    WHERE s.name = c.drinker OR s.victim = c.drinker) A ON b.name = A.bar
    GROUP BY b.name
    ORDER BY rating
	</pre>

	<h4>Offender Predictions (strikeNext.php)</h4>
	<p>
		The query finds sex offenders who left with a person at a date after the original crime 
		date and consumed equal or greater amount of alcohol as compared to the day on 
		which the sex offender left the bar with someone.
	</p>
	<pre class="brush: sql">
	SELECT D.drinker AS criminal, C.dateOfCrime, LEFT(D.dateOfConsump,10) AS anotherPotentialCrimeDate, 
		  C.numDrinks AS numDrinksOnDayOfOffense, D.numDrinks AS numDrinksOnAnyDayAfterOffense 
	FROM 
		(SELECT A.name, B.numDrinks, A.dateOfCrime FROM SexOffender A, Consumed B 
		WHERE A.name=B.drinker AND A.dateOfCrime=LEFT(B.dateOfConsump,10)) C, Consumed D 
	WHERE 
	C.name=D.drinker AND C.dateOfCrime&lt;LEFT(D.dateOfConsump,10) AND C.numDrinks&lt;=D.numDrinks 
	ORDER BY D.drinker
	</pre>
<h4>Unreported Offenses (unreported.php)</h4>
<p>The query lists sex offenders who consumed alcohol on day of their crime
and left a bar with a person that was not their victim.</p>
<pre class="brush: sql">
SELECT B.dateOfCrime, A.numDrinks, B.name AS criminal, B.victim AS recordedVictim,  
IF(A.drinker=C.drinker1, C.drinker2, C.drinker1) AS potentialVictim 
FROM Consumed A, SexOffender B, LeftWith C 
WHERE LEFT(C.dateOccurred,10)=B.dateOfCrime 
AND (A.drinker=C.drinker1 OR A.drinker=C.drinker2) 
AND A.drinker=B.name 
AND LEFT(A.dateOfConsump, 10)=B.dateOfCrime 
AND A.drinker IN 
  (SELECT DISTINCT name FROM SexOffender) AND B.victim &lt;> 
  	IF(A.drinker=C.drinker1, C.drinker2, C.drinker1)
</pre>
</div>
<h3 data-link="dataGeneration" class="expand-link">Data Generation <span>expand</span></h3>
<div data-box="dataGeneration" class="expand-box">
	<p>The files responsible for each portion of generation is listed next to the names below</p>
	<h4>Cities (cities.py, city_scrape.js)</h4>
	<ul>
		<li>Cities were taken from wikipedia's list of municipalities in NJ (<a href="http://en.wikipedia.org/wiki/List_of_municipalities_in_New_Jersey" target="_blank" title="Open in new tab">http://en.wikipedia.org/wiki/List_of_municipalities_in_New_Jersey</a>) using city_scrape.js</li>
		<li>Afterwards, we used the Google Geocode API (via the cities.py script) to get the longitude and latitude</li>
		<li>The methods cities.py offers will add approximately a random 10 miles around the longitude and latitude of the city to give the randomness of bar/drinker locations</li>
	</ul>
	<h4>Bar Table (bars.py, cities.py)</h4>
	<ul>
		<li>Random numbers for phone number, random string of characters for license</li>
		<li>City and longitude/latitude were from cities.py</li>
		<li>The bar names are randomly generated from three lists of 'bar-name sounding' words</li>
		<li>We also added an extra forty Polish bars manually (which have a value of 1 for international)</li>
	</ul>
	<h4>Drinker Table (drinkers.py, reinsertFemales.py)</h4>
	<ul>
		<li>Street name scraped from <a href="http://www.mess.be/inickgenwuname.php" target="_blank" title="Open in new tab">http://www.mess.be/inickgenwuname.php</a></li>
		<li>City is from cities.py</li>
		<li>Found a list of names online and randomly connected first names and last names</li>
		<li>Age is a random number from 17 to 80 (underage drinkers were using in our patterns)</li>
		<li>After we altered our data, we realized we had lost all females so we wrote reinsertFemales.py which made 30% of drinkers female</li>
	</ul>
	<h4>Frequents Table (frequents.py)</h4>
	<ul>
		<li>For each drinker, we chose a certain amount of bars from the city they live in and had them at least visit one and gave a high chance of visiting a few more</li>
		<li>After, they had a small chance of visiting another random bar (from anywhere in NJ) to simulate people travelling</li>
	</ul>
	
	<h4>Left With Table (leftwith.py)</h4>
	<ul>
		<li>This table describes when two drinkers left a bar together</li>
		<li>Two drinkers were chosen at random using frequents (they must frequent the same bar) and matched up</li>
		<li>Not all drinkers necessarily leave with other drinkers</li>
	</ul>
	
	<h4>Sells (sells.py, sells_redemption.py, beer_price.py)</h4>
	<ul>
		<li>Our original sells.py script randomly assigned a beer to a bar</li>
		<li>Due to many bars not selling any beers, we wrote sells_redemption.py which enforces all bars to sell at least one beer</li>
		<li>Price for every beer was generated by selecting beer alcohol content and assigning a price. The higher the alcohol content the higher the price.</li>
	</ul>
	
	<h4>Likes (likes.py)</h4>
	<ul>
		<li>The script randomly assigns a beer to a drinker</li>
		<li>We weight Polish beers to be liked 40% of the time</li>
	</ul>
	<h4>Beer, Country, Manufacturer Tables (beer_country_manf.py)</h4>
	<ul>
		<li>beer name, manufacturer, and country information was scrapted from (www.beerme.com/beerlist.php)</li>
		<li>beer name, manufacturer, and style were scrapted from the page, alcohol content was generated randomly between 1%-10% inclusive</li>
		<li>Manufacturer and Country tables followed from the scrapted data</li>
		<li>a few countries were manually labeled as having prohibition</li>
	</ul>
	<h4>Consumed Table (consumed.py)</h4>
	<ul>
		<li>
			the script pulled the data from the LeftWith table
			<ul>
			<li>the date was based on the date from the LeftWith table and the time was anywhere between 1-120 minutes before the time at which the individuals left the bar</li>
			<li>drinker1 from LeftWith table was selected to consume 1-4 drinks</li>
			<li>drinker2 from LeftWith table was selected to consume 4-10 drinks</li>
			<li>only one entry was generated for each unique drinker from the LeftWith table</li>
			</ul>	
		</li>
		<li>
			the rest of data was pulled from the Drinkers table and selected those that did not appear in LeftWith
			<ul>
				<li>for each drinker, an assumption was made that a drinker would go to a bar 1-4 times over a course of a month</li>
				<li>each drinker would be generated to consume anywhere between 1-10 drinks during one visit, date of consumption was random</li>
			</ul>
		</li>
	</ul>

	<h4>SexOffender Table (sexOffenders.py)</h4>
	<ul>
		<li>Out of the offenses generated, 80% of them occured on the weekend (Thursday, Friday, Saturday</li>
		<li>Each sex offender attacked anywhere between 1-2 times</li>
		<li>The script selected 220 random male drinkers (210 assult females, 10 assult males)</li>
		<li>The script selected 20 random female drinkers (5 assult females, 15 assult males)</li>
		<li>10 male victims were made into sexual offenders and assulted females</li>
		<li>2 female victims were made into sexual offenders and assulted males</li>
	</ul>
</div>
<script>
SyntaxHighlighter.all();
function toggle(link, cb){
	var ln = link.attr("data-link");
	var icon = link.find("span");
	var val = icon.html();
	var links = $(".expand-link");

	
	if(val == "collapse"){
		//close
		$(".expand-box[data-box=" + ln + "]").slideUp(300, cb);
		icon.html("expand");
	}
	else{
		for(var i = 0; i < links.length; i++){
			var tmp = $(links[i]);
			var box = $(".expand-box[data-box=" + tmp.attr("data-link") + "]");
			if(tmp.find("span").html() == "collapse"){
				if(tmp != link){
					box.slideUp(300);
					tmp.find("span").html("expand");
				}
			}
		}
		$(".expand-box[data-box=" + ln + "]").slideDown(300, cb);
		icon.html("collapse");
	}
}
$(".expand-link").on("click", function(){
	toggle($(this));
});
$(".verify").on("click", function(){
	var but = $(this);
	var loader = but.parent().find("img");
	var out = but.parent().find("span");
	var which = but.attr("data-which");
	out.html("Verifying...");
	if(which == "alc" || which == "underage"){
		out.html("Verifying... please wait, this pattern takes ~40 seconds to verify.")
	}
	loader.show();
	$.ajax({
		url: "verify.php?pattern=" + which,
		dataType: "text",
		success: function(data, a, b){
			if(data == "Yes"){
				out.html("Pattern was verified!");
			}
			else if(data == "No"){
				out.html("Pattern was not verified.");
			}
			else if(data == "ERROR"){
				out.html("Unknown error occurred.");
			}
			loader.hide();
		}
	});
});
toggle($(".expand-link[data-link=target]"));
</script>
<?php require_once("res/bottom.php"); ?>
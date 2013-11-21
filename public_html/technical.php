<?php 
	require_once("settings.php");
	$PG_NAME = "technical";
	require_once("res/top.php"); //header, navigation, opening tags etc.
?>
<script src="res/shCore.js"></script>
<script src="res/shBrushSql.js"></script>
<h3>Target Audience and Business Aspects</h3>
<h3>Data Generation</h3>
<h4>Cities (cities.py)</h4>
<ul>
	<li>Cities were taken from wikipedia's list of municipalities in NJ (<a href="http://en.wikipedia.org/wiki/List_of_municipalities_in_New_Jersey" target="_blank" title="Open in new tab">http://en.wikipedia.org/wiki/List_of_municipalities_in_New_Jersey</a>)</li>
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

<h3>Patterns</h3>
<h4>People who leave with underage drinkers are sex offenders</h4>

<p>This pattern states that if an older person left with an underage drinker the older person is a sex offender.</p>
<pre class="brush: sql">
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
</pre>

<h4>Bars which serve illegal beers have sex offenders</h4>
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

<h4>People who frequent international bars like at least one Polish beer</h4>
<p>Here is a verification of the pattern</p>
<pre class="brush: sql">
SELECT IF
((SELECT COUNT(f.drinker) FROM Frequents f, Bar b 
WHERE f.bar = b.name AND b.international='1' AND
(SELECT COUNT(*) FROM Likes l WHERE l.drinker = f.drinker AND l.beer IN(
SELECT b.name FROM Beer b, Manufacturer m
WHERE b.manf = m.name AND m.country = 'Poland')) = 0) = 0, 'Yes', 'No') AS verification
</pre>
<script>
SyntaxHighlighter.all();
</script>
<?php require_once("res/bottom.php"); ?>
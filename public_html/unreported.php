<?php
require_once("settings.php");
$PG_NAME = 'unreported';
require_once("res/top.php");

?>

<h4>This is a list of possible unreported offences based on the history of the sex offenders </h4>
<p class='opener'>From records of consumption and people they left with, this list contains possible unreported offenses that could use further investigation.</p>
<table class="niceTable" cellspacing="0">
	<thead>
		<tr>
			<th class="offenderName">Offender Name</th>
			<th>Potential Victim</th>
			<th>Date</th>
		</tr>
	</thead>
	<tbody>
<?php

//use this query for unreported offenses!! it lists the sexoffenders who consumed alcohol on day of their crime
//and left with a bar with a person that was not their victim
$q = "SELECT B.dateOfCrime, A.numDrinks, B.name AS criminal, B.victim AS recordedVictim,  
	  IF(A.drinker=C.drinker1, C.drinker2, C.drinker1) AS potentialVictim 
	  FROM Consumed A, SexOffender B, LeftWith C 
	  WHERE LEFT(C.dateOccurred,10)=B.dateOfCrime 
	  AND (A.drinker=C.drinker1 OR A.drinker=C.drinker2) 
	  AND A.drinker=B.name 
	  AND LEFT(A.dateOfConsump, 10)=B.dateOfCrime 
	  AND A.drinker IN 
	  (SELECT DISTINCT name FROM SexOffender) AND B.victim <> IF(A.drinker=C.drinker1, C.drinker2, C.drinker1)";
// ^NEW QUERY TO USE [Pawel]


/*OLD QUERY [Kevin]
$q = "SELECT DISTINCT dateOccurred, so.name as possibleCriminal, lw.drinker2 as possibleVictim 
FROM LeftWith lw, SexOffender so 
WHERE lw.drinker1 = so.name AND lw.drinker2 NOT IN (SELECT victim FROM SexOffender WHERE name=so.name) 

UNION

SELECT DISTINCT dateOccurred, so.name as possibleCriminal, lw.drinker1 as possibleVictim 
FROM LeftWith lw, SexOffender so 
WHERE lw.drinker2 = so.name AND lw.drinker1 NOT IN (SELECT victim FROM SexOffender WHERE name=so.name)";
*/

$results = mysql_query( $q) or die("Could not fetch sex offenders");

while($row = mysql_fetch_assoc($results)){
	printf("<tr><td><a href='predator.php?name=%s' title='View predator information'>%s</a></td><td>%s</td><td>%s</td></tr>", urlencode($row['criminal']), $row['criminal'], $row['potentialVictim'], $row['dateOfCrime']);
}
echo "</tbody></table><br/>";
require_once("res/bottom.php");
?>
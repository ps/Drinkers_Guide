<h2>This is a list of possible unreported offences based on the history of the sex offenders </h2>
<?php
require_once("settings.php");

$q = "SELECT DISTINCT dateOccurred, so.name as possibleCriminal, lw.drinker2 as possibleVictim 
FROM LeftWith lw, SexOffender so 
WHERE lw.drinker1 = so.name AND lw.drinker2 NOT IN (SELECT victim FROM SexOffender WHERE name=so.name) 

UNION

SELECT DISTINCT dateOccurred, so.name as possibleCriminal, lw.drinker1 as possibleVictim 
FROM LeftWith lw, SexOffender so 
WHERE lw.drinker2 = so.name AND lw.drinker1 NOT IN (SELECT victim FROM SexOffender WHERE name=so.name)";

$results = mysqli_query($cxn, $q) or die("Could not fetch sex offenders");

while($row = mysqli_fetch_assoc($results)){
	printf("%s | %s | %s<br/>", $row['possibleCriminal'], $row['possibleVictim'], $row['dateOccurred']);
}
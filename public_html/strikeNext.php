<?php 

require_once("settings.php");
$PG_NAME = 'strikeNext';
require_once("res/top.php");

	/*this query is being used in unreported.php
	/
	$q = "select A.numDrinks, B.name, B.victim, IF(A.drinker=C.drinker1, C.drinker2, C.drinker1) AS leWi from Consumed A, SexOffender B, LeftWith C where LEFT(C.dateOccurred,10)=B.dateOfCrime and (A.drinker=C.drinker1 or A.drinker=C.drinker2) and A.drinker=B.name and LEFT(A.dateOfConsump, 10)=B.dateOfCrime and A.drinker in (Select distinct name from SexOffender) and B.victim<>IF(A.drinker=C.drinker1, C.drinker2, C.drinker1)";
	*/
?>
<p class="opener">
	Drinker's Guide uses previous drinking patterns along with data regarding sexual offenders to 
	predict when some of the sex offenders potentially struck again after the initial offense, but were not caught.
</p>
<?php
//query for potentially 'struckNext'
$q = "SELECT D.drinker AS criminal, C.dateOfCrime, LEFT(D.dateOfConsump,10) AS anotherPotentialCrimeDate, 
	  C.numDrinks AS numDrinksOnDayOfOffense, D.numDrinks AS numDrinksOnAnyDayAfterOffense FROM 
	  (SELECT A.name, B.numDrinks, A.dateOfCrime FROM SexOffender A, Consumed B 
	  WHERE A.name=B.drinker AND A.dateOfCrime=LEFT(B.dateOfConsump,10)) C, 
	  Consumed D WHERE C.name=D.drinker AND C.dateOfCrime<LEFT(D.dateOfConsump,10) AND C.numDrinks<=D.numDrinks 
	  ORDER BY D.drinker";

$query = mysql_query( $q) or die("Query failed: ".mysql_error());

echo "<table class='niceTable modified' cellspacing='0'><thead><tr><th class='barName'>Criminal</th><th>Date Of Offense</th><th>Potentially Struck Again On</th>";
echo "</tr></thead><tbody>";

while($row = mysql_fetch_array($query))
{
	echo "<tr><td><a href='predator.php?name=".urlencode($row["criminal"])."' title='View predator'>".$row["criminal"]."</a></td><td>".$row["dateOfCrime"]."</td><td>".$row["anotherPotentialCrimeDate"]."</td></tr>";
}
echo "</tbody></table><br>";

require_once("res/bottom.php");

?>


<?php 

require_once("settings.php");
$PG_NAME = 'strikeNext';
require_once("res/top.php");

	/*this query is being used in unreported.php
	/
	$q = "select A.numDrinks, B.name, B.victim, IF(A.drinker=C.drinker1, C.drinker2, C.drinker1) AS leWi from Consumed A, SexOffender B, LeftWith C where LEFT(C.dateOccurred,10)=B.dateOfCrime and (A.drinker=C.drinker1 or A.drinker=C.drinker2) and A.drinker=B.name and LEFT(A.dateOfConsump, 10)=B.dateOfCrime and A.drinker in (Select distinct name from SexOffender) and B.victim<>IF(A.drinker=C.drinker1, C.drinker2, C.drinker1)";
	*/
//query for potentially 'struckNext'
$q = "SELECT D.drinker AS criminal, C.dateOfCrime, LEFT(D.dateOfConsump,10) AS anotherPotentialCrimeDate, 
	  C.numDrinks AS numDrinksOnDayOfOffense, D.numDrinks AS numDrinksOnAnyDayAfterOffense FROM 
	  (SELECT A.name, B.numDrinks, A.dateOfCrime FROM SexOffender A, Consumed B 
	  WHERE A.name=B.drinker AND A.dateOfCrime=LEFT(B.dateOfConsump,10)) C, 
	  Consumed D WHERE C.name=D.drinker AND C.dateOfCrime<LEFT(D.dateOfConsump,10) AND C.numDrinks<=D.numDrinks";


$query = mysqli_query($cxn, $q) or die("Query failed: ".mysqli_error($cxn));
	
echo mysqli_num_rows($query);
while($row = mysqli_fetch_array($query))
{
	echo $row["criminal"]."<--->".$row["dateOfCrime"]."-->".$row["numDrinksOnDayOfOffense"]." | ".
		 $row["anotherPotentialCrimeDate"]."-->".$row["numDrinksOnAnyDayAfterOffense"]."<br>";
}

require_once("res/bottom.php");

?>


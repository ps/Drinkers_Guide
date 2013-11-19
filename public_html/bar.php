<?php 
	require_once("settings.php");
	echo "Safety Rating here: <br>";
	//$_GET["bar"]="Stone Bottle Cellar";
	$bname = $_GET["bar"];
?>
Maybe some pretty picture here<br><br>
Safety Rating Here: <br><br>
Recommend to a friend? [Yes/No] probably based on the rating<br><br>
Beers Served:
<?php
	$q = "SELECT beer FROM Sells WHERE bar='".$bname."'";

	$query = mysqli_query($cxn, $q) or die("Query failed: ".mysqli_error($cxn));
	$numBeers = mysqli_num_rows($query);
	echo $numBeers;

	while($row = mysqli_fetch_array($query))
	{
		echo $row["beer"]."<br>";
	}
?>
<br><br>
Illegal beers : 
<?php
	$q = "SELECT beer FROM Sells WHERE bar='".$bname."' AND beer IN (SELECT A.name FROM Beer A, Manufacturer B WHERE A.manf=B.name AND  B.country IN 
			(SELECT name FROM Country WHERE prohibition='1'))";

	$query = mysqli_query($cxn, $q) or die("Query failed: ".mysqli_error($cxn));
	$numIllegal = mysqli_num_rows($query);

	if($numIllegal>0)
	{
		//maybe display the beers?

	}
	echo $numIllegal;

?>
<br><br>
Sex Offenders Associated with Bar:
<?php
	$q = "SELECT drinker FROM Frequents WHERE bar='".$bname."' AND drinker IN (SELECT DISTINCT name FROM SexOffender)";

	$query = mysqli_query($cxn, $q) or die("Query failed: ".mysqli_error($cxn));

	$numOffenders = mysqli_num_rows($query);
	echo $numOffenders;

	while($row = mysqli_fetch_array($query))
	{
		echo $row["drinker"]."<br>";
	}
?>
<br><br>
Have any of sex offense victims that frequent this bar become sex offenders?
<?php
	$q = "SELECT drinker FROM Frequents WHERE bar='".$bname."' AND drinker IN 
	(SELECT DISTINCT victim FROM SexOffender WHERE victim IN (SELECT DISTINCT name FROM SexOffender))";

	$query = mysqli_query($cxn, $q) or die("Query failed: ".mysqli_error($cxn));
	$numVicOff = mysqli_num_rows($query);
	echo $numVicOff;

	while($row = mysqli_fetch_array($query))
	{
		echo $row["drinker"]."<br>";
	}
?>
<?php 
//experimental, try to check when sexOfennder will strike next based on drinking pattern
	require_once("settings.php");
/*
	$q = "SELECT A.name, B.numDrinks, B.dateOfConsump FROM (SELECT DISTINCT name FROM SexOffender) A, Consumed B WHERE A.name=B.drinker ORDER BY A.name, B.numDrinks";

	$query = mysqli_query($cxn, $q) or die("Query failed: ".mysqli_error($cxn));
	
	while($row = mysqli_fetch_array($query))
	{
		echo $row["name"]."<--->".$row["numDrinks"]."<--->".$row["dateOfConsump"]."<br>";
	}*/
?>

<?php 
//experimental, try to check when sexOfennder will strike next based on drinking pattern
//
//query below found 88 drinkers who consumed alcohol on the same day that they commited a SexOffender act
	require_once("settings.php");

	$q = "select B.name, B.victim, IF(A.drinker=C.drinker1, C.drinker2, C.drinker1) AS leWi from Consumed A, SexOffender B, LeftWith C where LEFT(C.dateOccurred,10)=B.dateOfCrime and (A.drinker=C.drinker1 or A.drinker=C.drinker2) and A.drinker=B.name and LEFT(A.dateOfConsump, 10)=B.dateOfCrime and A.drinker in (Select distinct name from SexOffender)";

//write another query that checks if it is likely that on the other days that the sex offenders from above frequented a bar
//they committed a sex act that was ureported (if consumed more alcohol or equal then on that day then yes, else no)

	$q = "select Z.name, Z.numDrinks AS drinksOnDateOfOffense, W.numDrinks AS drinksOnOtherDays from (select B.name, A.dateOfConsump, A.numDrinks from Consumed A, SexOffender B, LeftWith C where LEFT(C.dateOccurred,10)=B.dateOfCrime and (A.drinker=C.drinker1 or A.drinker=C.drinker2) and A.drinker=B.name and LEFT(A.dateOfConsump, 10)=B.dateOfCrime and A.drinker in (Select distinct name from SexOffender)) AS Z, Consumed W where Z.name=W.drinker and Z.dateOfConsump <> W.dateOfConsump ";

	$query = mysqli_query($cxn, $q) or die("Query failed: ".mysqli_error($cxn));
	
	echo mysqli_num_rows($query);
	while($row = mysqli_fetch_array($query))
	{
		echo $row["name"]."<--->".$row["drinksOnDateOfOffense"]."<--->".$row["drinksOnOtherDays"]."<br>";
	}
?>


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
	require_once("settings.php");

	$q = "select B.name, B.victim, IF(A.drinker=C.drinker1, C.drinker2, C.drinker1) AS leWi from Consumed A, SexOffender B, LeftWith C where LEFT(C.dateOccurred,10)=B.dateOfCrime and (A.drinker=C.drinker1 or A.drinker=C.drinker2) and A.drinker=B.name and LEFT(A.dateOfConsump, 10)=B.dateOfCrime and A.drinker in (Select distinct name from SexOffender)";

	$query = mysqli_query($cxn, $q) or die("Query failed: ".mysqli_error($cxn));
	
	echo mysqli_num_rows($query);
	while($row = mysqli_fetch_array($query))
	{
		echo $row["name"]."<--->".$row["victim"]."<--->".$row["leWi"]."<br>";
	}
?>


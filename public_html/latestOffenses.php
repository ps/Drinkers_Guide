<?php 
	require_once("settings.php");
?>
(Probably add a picture next to the bastards' names)<br>
Lastest Offenses:<br>
<?php
	$q = "SELECT name, dateOfCrime FROM SexOffender ORDER BY dateOfCrime DESC LIMIT 0,10";

	$query = mysqli_query($cxn, $q) or die("Query failed: ".mysqli_error($cxn));
	
	while($row = mysqli_fetch_array($query))
	{
		echo $row["name"]."<--->".$row["dateOfCrime"]."<br>";
		$subQ = "SELECT bar FROM Frequents WHERE drinker='".$row["name"]."'";
		$subQuery = mysqli_query($cxn, $subQ) or die("Query failed: ".mysqli_error($cxn));
		echo "<ul>";
		while($subRow = mysqli_fetch_array($subQuery))
		{
			echo "<li>".$subRow["bar"]."</li>";
		}
		echo "</ul>";
	}
?>
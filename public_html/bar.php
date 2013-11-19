<?php 
	require_once("settings.php");
	echo "Safety Rating here: <br>";
	$_GET["bar"]="Bar name here"
	$bname = $_GET["bar"];
?>
Maybe some pretty picture here<br>
Safety Rating Here: <br>
Illegal beers : 
<?php
	$q = "SELECT * FROM Beer A, Manufacturer B WHERE A.manf=B.name AND  B.country IN (SELECT name FROM Country WHERE prohibition='1')";

	$query = mysqli_query($cxn, $q) or die("Query failed: ".mysqli_error($cxn));
	echo mysqli_num_rows($query);

	/*
	while ($row = mysqli_fetch_array($query))
	{
		print_r($row);
		
		echo "<br>";
	}*/
?>

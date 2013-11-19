<?php 
error_reporting(E_ALL);
ini_set("display_errors",1);
	require_once("settings.php");

	//query to check if victim got revenge on the criminal [currently no such pattern is present :( ]
	$q = "SELECT DISTINCT A.name AS CRIMINAL, A.victim AS VICTIM FROM SexOffender A WHERE A.name IN (SELECT DISTINCT victim FROM SexOffender WHERE name=A.victim) 
		  AND A.victim IN (SELECT DISTINCT name FROM SexOffender WHERE name=A.name)";

	//query to verify pattern that certain victims became SexOffenders
	$q = "SELECT DISTINCT victim FROM SexOffender WHERE victim IN (SELECT DISTINCT name FROM SexOffender)";

	$query = mysqli_query($cxn, $q) or die("Query failed: ".mysqli_error($cxn));
	//echo mysqli_num_rows($query);
	while ($row = mysqli_fetch_array($query))
	{
		//print_r($row);
		
		//echo "<br>";
	}


	//Select the safest and most dangerous bars of the month
	$results = getAllRatings(true);
	$leastSafe = mysqli_fetch_assoc($results);
	$safest = null;
	while($tmp = mysqli_fetch_assoc($results)){
		$safest = $tmp;
	}

?>
<html>
<head>
	<title>Bar Safety</title>
	<link href="style.css" rel="stylesheet" type="text/css" />

</head>
<body>
	<div id="container">
		<div>Pretend this is the main content page</div>
		<strong>
			<?php
				printf("Safest bar is %s <br />", $safest['name']);
				printf("Most dangerous bar is %s <br />", $leastSafe['name']);
			?>
		</strong>
		<div>
			<u>Menu Shizz</u>
			<br>
			<a href="">Top 10 Safest Bars</a>
			<br>
			<a href="">Top 10 Least Safest Bars</a>
			<br>
			Search bar:
			<form action="search.php" method="GET">
				Location (within NJ): <input type="text" name="location"><br>
				Bar name: <input type="text" name="bname">
				<input type="submit" value="Find">		
			</form> 
		
			<a href="">View Latest Offenses</a><br>
			<a href="">Upreported Sex Offenders</a><br>
			<a href="">Top 10 Dangerous Offenders</a><br>
			<a href="">When will offender strike next?</a><br>
			<br><br>
			<a href="">HeatMap Dawg</a>
		
		</div>
	</div>
	
</body>
</html>
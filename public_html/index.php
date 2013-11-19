<?php 
	require_once("settings.php");

	//query to check if victim got revenge on the criminal [currently no such pattern is present :( ]
	$q = "SELECT DISTINCT A.name AS CRIMINAL, A.victim AS VICTIM FROM SexOffender A WHERE A.name IN (SELECT DISTINCT victim FROM SexOffender WHERE name=A.victim) 
		  AND A.victim IN (SELECT DISTINCT name FROM SexOffender WHERE name=A.name)";

	//query to verify pattern that certain victims became SexOffenders
	$q = "SELECT DISTINCT victim FROM SexOffender WHERE victim IN (SELECT DISTINCT name FROM SexOffender)";

	$query = mysqli_query($cxn, $q) or die("Query failed: ".mysqli_error($cxn));
	echo mysqli_num_rows($query);
	while ($row = mysqli_fetch_array($query))
	{
		print_r($row);
		
		echo "<br>";
	}

?>
<html>
<head>
</head>
<body>
	<div>Pretend this is the main content page</div>
	<br><br><br><br>
	<div>
		<u>Menu Shizz</u>
		<br>
		<a href="">Top 10 Safest Bars</a>
		<br>
		<a href="">Top 10 Least Safest Bars</a>
		<br>
		Search bar:
		<form>
			Location: <input type="text" name="location"><br>
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
	
</body>
</html>
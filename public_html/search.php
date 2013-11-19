<?php
$PG_NAME='search';
require_once("res/top.php");
?>

<form action="search.php" method="GET">
	Location: <input type="text" name="location"><br>
	Bar name: <input type="text" name="bname">
	<input type="submit" value="Find">		
</form> 

<?php
/*
TODO: add maps
*/
require_once("settings.php");

$barName = "";
$location = "";
if(isset($_GET['bname']) && isset($_GET['location']) && $_GET['location'] != 'Search by location (in NJ)'){
	$barName = trim($_GET['bname']) . " "; //extra space since data has it
	$location = trim($_GET['location']);
	if($location != ""){
		if(!stristr($location, "NJ") && !stristr($location, "New Jersey")){
			$location .= ",NJ";
		}
		echo "<h2>Search by location</h2>";
		//search by location
		$results = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=" . urlencode($location));
		$results = json_decode($results, true);
		$lon = $results["results"][0]["geometry"]["location"]["lng"];
		$lat = $results["results"][0]["geometry"]["location"]["lat"];
		$q = sprintf("SELECT name, SQRT(POW(longitude - %f, 2) + POW(latitude - %f, 2)) AS dist, city FROM Bar ORDER BY dist;", $lon, $lat);
		$results = mysqli_query($cxn, $q) or die("Could not find closest bars");
		//select top 10
		$i = 10;
		while($i > 0 && $row = mysqli_fetch_assoc($results)){
			//get the safety rating for each bar, plot on map
			printf("%s | %f | %s<br/>" , $row['name'], $row['dist'], $row['city']);
			$i--;
		}
	}
	else if($barName != "" && $barName != "Search by bar name"){
		//search by bar
		echo "<h2>Search by bar name</h2>";
		$terms = implode("%", explode(" ", $barName));
		$q = "SELECT name, city FROM Bar WHERE name LIKE '%" . $terms . "%'";
		$results = mysqli_query($cxn, $q) or die("Could not find bars");
		while($row = mysqli_fetch_assoc($results)){
			printf("%s|%s<br/>", $row['name'], $row['city']);
		}
	}
}
else{
	die("Nothing submitted");
}
require_once("res/bottom.php");

<?php
require_once("settings.php");
if(!isset($_GET['name'])){
	die("No predator name specified.");
}
$predator_name = mysqli_escape_string($cxn, trim($_GET['name']));
$predator_img = "";
$predator_history = NULL; //result object
$dangerRating = 0;

//Show this predator's information, and show a strike-zone map (also possibly include his location)

$q = "SELECT so.name AS name, d.gender AS gender, COUNT(*) as numOffenses, d.latitude AS lat, d.longitude AS lon, MAX(SQRT(POW(d.latitude - b.latitude,2) + POW(d.longitude - b.longitude, 2))) AS radius FROM SexOffender so, Drinker d, Frequents f, Bar b WHERE so.name = f.drinker AND f.bar = b.name AND d.name=so.name AND so.name='" . $predator_name . "' GROUP BY so.name";
$results = mysqli_query($cxn, $q) or die("Could not get sex offender's information");
$row = mysqli_fetch_assoc($results);

$imgFile = "res/images_females";
if($row['gender'] == 'M'){
	$imgFile = "res/images_males";
}

print_r($row);
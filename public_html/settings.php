<?php
$cxn = mysqli_connect("cs336-9.cs.rutgers.edu", "root", "root", "SAFETY_DB") or die("Could not connect to database server");


//To increase number of bars with lower safety rating, we can add more illegal beers to sells (as long as we update the illegal beer pattern)

// (Get # sex offenders for each bar + # of illegal beers) / #total people who frequent
function getAllRatings($sort){
	global $cxn;
	$q = "SELECT b.name AS name, round((10 - (COUNT(A.name) + (SELECT COUNT(*)  FROM Sells s  WHERE s.bar = b.name AND s.beer IN (SELECT name FROM Beer where manf in (SELECT m.name FROM Manufacturer m,Country c Where m.country = c.name AND prohibition=1)))) * (10/13)),1) AS rating FROM Bar b LEFT JOIN (SELECT c.bar AS bar, s.name AS name FROM SexOffender s, Frequents c WHERE s.name = c.drinker OR s.victim = c.drinker) A ON b.name = A.bar GROUP BY b.name";
	if($sort){
		$q .= " ORDER BY rating";
	}
	$results = mysqli_query($cxn, $q) or die("Could not fetch ratings");
	return $results;
}

//$results[0]-->bar name
//$resutls[1]-->rating
function getOneRating($bName){
	global $cxn;
	$bName = mysqli_escape_string($cxn, $bName);
	$q = "SELECT b.name AS name, round((10 - (COUNT(A.name) + (SELECT COUNT(*)  FROM Sells s  WHERE s.bar = b.name AND s.beer IN (SELECT name FROM Beer where manf in (SELECT m.name FROM Manufacturer m,Country c Where m.country = c.name AND prohibition=1)))) * (10/13)),1) AS rating FROM Bar b LEFT JOIN (SELECT c.bar AS bar, s.name AS name FROM SexOffender s, Frequents c WHERE s.name = c.drinker OR s.victim = c.drinker) A ON b.name = A.bar WHERE b.name = '". $bName ."' GROUP BY b.name;";
	$results = mysqli_query($cxn, $q) or die("Could not fetch rating");
	return $results;
}
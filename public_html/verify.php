<?php
require_once("settings.php");
if(!isset($_GET['pattern'])){
	die("ERROR");
}

switch($_GET['pattern']){
	case "alc":
		$results = mysql_query("SELECT CASE WHEN (SELECT COUNT(*) FROM (
		SELECT A.beer FROM 
		(SELECT * FROM Sells B, Beer C WHERE B.beer=C.name) A, 
		(SELECT * FROM Sells B, Beer C WHERE B.beer=C.name) D 
		WHERE (ABS(A.alcContent-D.alcContent)<=0.01 OR A.alcContent > D.alcContent) 
		AND A.beer<>D.beer 
		AND A.price < D.price
	    ) A) = 0 THEN 'Yes'
		ELSE 'No'
		END AS isTrue") or die("ERROR");	
	break;
	case "underage":
		$results = mysql_query("SELECT CASE WHEN (SELECT COUNT(*) FROM (
		SELECT d1.name
		FROM LeftWith lw, Drinker d1, Drinker d2 
		WHERE lw.drinker1 = d1.name AND d1.age >= 21 AND lw.drinker2 = d2.name and d2.age < 21 AND NOT EXISTS(SELECT * from SexOffender s WHERE s.name = d1.name)
		UNION
		SELECT d1.name
		FROM LeftWith lw, Drinker d1, Drinker d2 
		WHERE lw.drinker2 = d1.name AND d1.age >= 21 AND lw.drinker1 = d2.name and d2.age < 21 AND NOT EXISTS(SELECT * from SexOffender s WHERE s.name = d1.name)
		) A) = 0 THEN 'Yes'
		ELSE 'No'
		END AS isTrue") or die("ERROR");
	break;
	case "offender":
		$results = mysql_query("SELECT CASE WHEN 
		(SELECT COUNT(*) 
		FROM Sells s 
		WHERE s.beer IN (SELECT name FROM Beer where manf in (SELECT m.name FROM Manufacturer m,Country c Where m.country = c.name AND prohibition=1))
		AND NOT EXISTS (SELECT * FROM SexOffender WHERE bar = s.bar)) = 0 THEN 'Yes'
		ELSE 'No'
		END AS isTrue") or die("ERROR");
	break;
	case "polish":
		$results = mysql_query("SELECT IF
		((SELECT COUNT(f.drinker) FROM Frequents f, Bar b 
		WHERE f.bar = b.name AND b.international='1' AND
		(SELECT COUNT(*) FROM Likes l WHERE l.drinker = f.drinker AND l.beer IN(
		SELECT b.name FROM Beer b, Manufacturer m
		WHERE b.manf = m.name AND m.country = 'Poland')) = 0) = 0, 'Yes', 'No') AS isTrue") or die("ERROR");
	break;
	default:
	die("ERROR");
	break;
}

$row = mysql_fetch_assoc($results);
		exit($row['isTrue']);
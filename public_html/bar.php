<?php 
	require_once("settings.php");
	$PG_NAME = "bar";
	require_once("res/top.php");
	if(!isset($_GET['bar']) || trim($_GET['bar']) == ""){
		niceDie("Bar not set");
	}
	//$_GET["bar"]="Stone Bottle Cellar";

	$bname = trim($_GET["bar"]);
	//$results = getOneRating($bname);
	//if(mysqli_num_rows($results) == 0){
	//	niceDie("Bar not found");
	//}
	//$row = mysqli_fetch_assoc($results);
	//$rating = $row['rating'];
	$rating = 9;
?>
<h1><?php echo $bname; ?></h1>
<h2>Safety Rating: <div class="ratingCircle r<?php echo round($rating); ?>"><?php echo $rating; ?></div></h2>
<?php
	$q = "SELECT beer FROM Sells WHERE bar='".$bname."'";

	$query = mysqli_query($cxn, $q) or die("Query failed: ".mysqli_error($cxn));
	$numBeers = mysqli_num_rows($query);
?>
<br/>
<div class="columns cf">
	<div class="left50">
		<h4>Beers Offered (<?php echo $numBeers; ?>)</h4>
		<?php
			while($row = mysqli_fetch_array($query))
			{
				echo $row["beer"]." <b>" . $row['price'] ."</b><br>";
			}
		?>
		
		<?php
			$q = "SELECT beer FROM Sells WHERE bar='".$bname."' AND beer IN (SELECT A.name FROM Beer A, Manufacturer B WHERE A.manf=B.name AND B.country IN 
					(SELECT name FROM Country WHERE prohibition='1'))";

			$query = mysqli_query($cxn, $q) or niceDie("Query failed: ".mysqli_error($cxn));
			$numIllegal = mysqli_num_rows($query);
			if($numIllegal>0)
			{
				echo "<h1>Caution, this Bar Sells the Following Illegal Beers</h1>";

			}
			echo $numIllegal;

		?>

	</div>
	<div class="right50">
		<h4>Sex Offenders who Frequent</h4>
	</div>
</div>
<br><br>
Recommend to a friend? [Yes/No] probably based on the rating<br><br>
Beers Served:

<br><br>

<br><br>
Sex Offenders Associated with Bar:
<?php
	$q = "SELECT drinker FROM Frequents WHERE bar='".$bname."' AND drinker IN (SELECT DISTINCT name FROM SexOffender)";

	$query = mysqli_query($cxn, $q) or die("Query failed: ".mysqli_error($cxn));

	$numOffenders = mysqli_num_rows($query);
	echo $numOffenders;

	while($row = mysqli_fetch_array($query))
	{
		echo $row["drinker"]."<br>";
	}
?>
<br><br>
Have any of sex offense victims that frequent this bar become sex offenders?
<?php
	$q = "SELECT drinker FROM Frequents WHERE bar='".$bname."' AND drinker IN 
	(SELECT DISTINCT victim FROM SexOffender WHERE victim IN (SELECT DISTINCT name FROM SexOffender))";

	$query = mysqli_query($cxn, $q) or die("Query failed: ".mysqli_error($cxn));
	$numVicOff = mysqli_num_rows($query);
	echo $numVicOff;

	while($row = mysqli_fetch_array($query))
	{
		echo $row["drinker"]."<br>";
	}
	require_once("res/bottom.php");
?>
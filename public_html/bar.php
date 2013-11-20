<?php 
	require_once("settings.php");
	$PG_NAME = "bar";
	require_once("res/top.php");
	if(!isset($_GET['bar']) || trim($_GET['bar']) == ""){
		niceDie("Bar not set");
	}
	//$_GET["bar"]="Stone Bottle Cellar";

	$bname = trim($_GET["bar"]);
	$results = getOneRating($bname);
	if(mysqli_num_rows($results) == 0){
		niceDie("Bar not found");
	}
	$row = mysqli_fetch_assoc($results);
	$rating = $row['rating'];
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
		<ul>
		<?php
			while($row = mysqli_fetch_array($query))
			{
				echo "<li>". $row["beer"]. "</li>";
			}
		?>
		</ul><br/>
		<?php
			$q = "SELECT beer FROM Sells WHERE bar='".$bname."' AND beer IN (SELECT A.name FROM Beer A, Manufacturer B WHERE A.manf=B.name AND B.country IN 
					(SELECT name FROM Country WHERE prohibition='1'))";

			$query = mysqli_query($cxn, $q) or niceDie("Query failed: ".mysqli_error($cxn));
			$numIllegal = mysqli_num_rows($query);
			if($numIllegal>0)
			{
				echo "<h4>Caution, this Bar Sells the Following Illegal Beers</h4><ul>";
				while($row = mysqli_fetch_assoc($query)){
					echo "<li>". $row["beer"]. "</li>";
				}
				echo "</ul>";
			}
			else{
				echo "<h4>This Bar Sells No Illegal Beers</h4>";
			}
		?>

	</div>
	<div class="right50">
		<?php
			$q = "SELECT drinker FROM Frequents WHERE bar='".$bname."' AND drinker IN (SELECT DISTINCT name FROM SexOffender)";

			$query = mysqli_query($cxn, $q) or die("Query failed: ".mysqli_error($cxn));

			$numOffenders = mysqli_num_rows($query);
		?>
		<h4>Sex Offenders who Frequent (<?php echo $numOffenders; ?>)</h4>
		<ul>
		<?php
			while($row = mysqli_fetch_array($query))
			{
				echo "<li>" . $row["drinker"]."</li>";
			}
		?>
		</ul>
	</div>
</div>


<?php
	$q = "SELECT drinker FROM Frequents WHERE bar='".$bname."' AND drinker IN 
	(SELECT DISTINCT victim FROM SexOffender WHERE victim IN (SELECT DISTINCT name FROM SexOffender))";

	$query = mysqli_query($cxn, $q) or die("Query failed: ".mysqli_error($cxn));
	$numVicOff = mysqli_num_rows($query);
	if($numVicOff > 0){
		echo "<h4>The Following Victims from this Bar Later became Sex Offenders</h4><ul class='marginFix'>";
	}
	else{
		echo "<h4>Victims from this Bar did not Later become Sex Offenders</h4>";
	}

	while($row = mysqli_fetch_array($query))
	{
		echo "<li>" . $row["drinker"]."</li>";
	}

	if($numVicOff > 0){
		echo "</ul>";
	}
	require_once("res/bottom.php");
?>
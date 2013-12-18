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
	if(mysql_num_rows($results) == 0){
		niceDie("Bar not found");
	}
	$bname = mysql_escape_string($bname);
	$row = mysql_fetch_assoc($results);
	$rating = $row['rating'];
?>
<h1><?php echo $bname; ?></h1>
<address>
<?php echo $row['city'] . ", NJ"; ?>
</address>
<h2>Safety Rating: <div class="ratingCircle r<?php echo round($rating); ?>"><?php echo $rating; ?></div></h2>

<?php
	$q = "SELECT s.beer AS beer, s.price AS price, b.style AS style, b.alcContent AS ac FROM Sells s, Beer b WHERE s.bar='".$bname."' AND b.name = s.beer";

	$query = mysql_query( $q) or die("Query failed: ".mysql_error());
	$numBeers = mysql_num_rows($query);
?>
<br/>
<div class="columns cf">
	<div class="left50">
		<h4>Beers Offered (<?php echo $numBeers; ?>)</h4>
		<table class="niceTable blue" cellspacing="0">
			<thead>
				<tr>
					<th class="beerName">Beer Name</th>
					<th>Price&nbsp;($)</th>
					<th>Alcohol Content (%)</th>
					<th>Style</th>
				</tr>
			</thead>
			<tbody>
		<?php
			while($row = mysql_fetch_array($query))
			{
				printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", $row['beer'], $row['price'], $row['ac'], $row['style']);
			}
		?>
			</tbody>
		</table>
		<br/>
		<?php
			$q = "SELECT beer FROM Sells WHERE bar='".$bname."' AND beer IN (SELECT A.name FROM Beer A, Manufacturer B WHERE A.manf=B.name AND B.country IN 
					(SELECT name FROM Country WHERE prohibition='1'))";

			$query = mysql_query( $q) or niceDie("Query failed: ".mysql_error());
			$numIllegal = mysql_num_rows($query);
			if($numIllegal>0)
			{
				echo "<h4>Caution, this Bar Sells the Following Illegal Beers</h4><ul>";
				while($row = mysql_fetch_assoc($query)){
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

			$query = mysql_query( $q) or die("Query failed: ".mysql_error());

			$numOffenders = mysql_num_rows($query);
		?>
		<h4>Sex Offenders who Frequent (<?php echo $numOffenders; ?>)</h4>
		<ul>
		<?php
			while($row = mysql_fetch_array($query))
			{
				echo "<li><a href='predator.php?name=". urlencode($row['drinker']) . "' title='View predator'>" . $row["drinker"]."</a></li>";
			}
		?>
		</ul>
	</div>
</div>


<?php
	$q = "SELECT drinker FROM Frequents WHERE bar='".$bname."' AND drinker IN 
	(SELECT DISTINCT victim FROM SexOffender WHERE victim IN (SELECT DISTINCT name FROM SexOffender))";

	$query = mysql_query( $q) or die("Query failed: ".mysql_error());
	$numVicOff = mysql_num_rows($query);
	if($numVicOff > 0){
		echo "<h4>The Following Victims from this Bar Later became Sex Offenders</h4><ul class='marginFix'>";
	}
	else{
		echo "<h4>Victims from this Bar did not Later become Sex Offenders</h4>";
	}

	while($row = mysql_fetch_array($query))
	{
		echo "<li><a href='predator.php?name=". urlencode($row['drinker']) . "' title='View predator'>" . $row["drinker"]."</a></li>";
	}

	if($numVicOff > 0){
		echo "</ul>";
	}
	require_once("res/bottom.php");
?>

<?php 
	require_once("settings.php");
	$PG_NAME = 'latestOffenses';
	require_once("res/top.php");
?>

<h4>Lastest Offenses</h4>
<p class='opener'>Keep on the look out for these recent sex offenders. More information about where they could be and how dangerous they are can be found by clicking on their names.</p>
<table class="niceTable" cellspacing="0">
	<thead>
		<tr>
			<th class="offenderName">Offender Name</th>
			<th>Date</th>
		</tr>
	</thead>
	<tbody>
<?php
	$q = "SELECT name, dateOfCrime FROM SexOffender ORDER BY dateOfCrime DESC LIMIT 0,10";

	$query = mysql_query( $q) or die("Query failed: ".mysql_error());
	
	while($row = mysql_fetch_array($query))
	{
		echo "<tr><td><a href='predator.php?name=" . urlencode($row['name']) . "'>" . $row['name'] . "</a></td><td>" . $row['dateOfCrime'] . "</td></tr>";
	}
?>
</tbody>
		</table>
<?php
	require_once("res/bottom.php");
?>
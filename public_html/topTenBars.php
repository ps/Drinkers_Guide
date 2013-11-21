<?php
require_once("settings.php");
$PG_NAME = 'topTenBars';
require_once("res/top.php");

$results = getAllRatings(true);
$total = mysql_num_rows($results);

?>

<div class="columns cf">
	<div class="left50">
		<h4>Top Ten Most Dangerous Bars</h4>
		<table class="niceTable" cellspacing="0">
			<thead>
				<tr>
					<th class="barName">Bar Name</th>
					<th>Safety Rating</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$i = 10;
					while($i > 0 && $row = mysql_fetch_assoc($results)){
						printf("<tr><td><a href='bar.php?bar=%s' title='View bar'>%s</a></td><td>%s</td></tr>", urlencode($row['name']), $row['name'], $row['rating']);
						$total--;
						$i--;
					}
				?>
			</tbody>
		</table>
	</div>
	<div class="right50">
		<h4>Top Ten Safest Bars</h4>
		<table class="niceTable blue" cellspacing="0">
			<thead>
				<tr>
					<th class="barName">Bar Name</th>
					<th>Safety Rating</th>
				</tr>
			</thead>
			<tbody>
				<?php
					while($total > 10){
						$row = mysql_fetch_row($results);
						$total--;
					}
					while($row = mysql_fetch_assoc($results)){
						printf("<tr><td><a href='bar.php?bar=%s' title='View bar'>%s</a></td><td>%s</td></tr>", urlencode($row['name']), $row['name'], $row['rating']);
						$total--;
						$i--;
					}
				?>
			</tbody>
		</table>
	</div>
</div>
<?php
require_once("res/bottom.php");
?>
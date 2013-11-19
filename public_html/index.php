<?php 
	require_once("settings.php");
	//Select the safest and most dangerous bars of the month
	/*
	$results = getAllRatings(true);
	$leastSafe = mysqli_fetch_assoc($results);
	$safest = null;
	while($tmp = mysqli_fetch_assoc($results)){
		$safest = $tmp;
	}
	*/
?>
<html>
<head>
	<title>Drinker's Guide - Bar Safety</title>
	<link href='http://fonts.googleapis.com/css?family=Noto+Sans:400,700' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Gentium+Book+Basic' rel='stylesheet' type='text/css'>
	<link href="res/style.css" rel="stylesheet" type="text/css" />
	<script src="res/jquery.js"></script>
	<script src="res/main.js"></script>
</head>
<body class="pg_home">
	<div id="bg"></div>
	<div id="container">
		<header id="page_header">
			<hgroup>
				<h1>Drinker's Guide</h1>
				<h2>Showing you the safest bars</h2>
			</hgroup>
		</header>
		<nav>
			<ul class="cf">
				<li><a class="ln_home" href="index.php">Home</a></li>
				<li><a class="ln_latestOffenses" href="latestOffences.php">Latest Offenses</a></li>
				<li><a class="ln_bestAndWorstBars" href="barStats.php">Bar Stats</a></li>
			</ul>
		</nav>
		<p class="opener">
			Drinker's Guide uses data collected monthly to administer safety ratings to bars in New Jersey. 
			Our goal is to provide a service which let's the consumer make more knowledgable choices when frequenting bars. 
			We also hope to provide helpful information to Police about possible unreported offenses and likelihood of future offenses.
		</p>
		<div class="columns cf">
			<div class="left50">
				<h4>Find the safest bars near you</h4>
				<form class="search" action="search.php" method="GET">
					<div class="searchbox">
						<label>Search by location (in NJ)</label>
						<input type="text" name="location" data-placeholder="Search by location (in NJ)"><br>
					</div>
					<div class="searchbox">
						<label>Search by bar name</label>
						<input type="text" name="bname" data-placeholder="Search by bar name">
					</div>
					<div class="submitbox">
						<input type="submit" value="Search">
					</div>
				</form>
				<br/>
				<h4>Ten most dangerous bars of the month</h4>
				<table cellspacing="0">
					<thead>
						<tr>
							<th class="barName">Bar Name</th>
							<th>Safety Rating*</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Bar 1</td><td>1.0</td>
						</tr>
						<tr>
							<td>Bar 2</td><td>1.0</td>
						</tr>
					</tbody>
				</table>
				<br/>
				<small>* Safety ratings are on a 0-10 scale with 0 being the most dangerous</small>
			</div>
			<div class="right50">
				<h4>Locations of known sex offenders</h4>
				<div id="map-canvas"></div>
			</div>
		</div>
		<strong>
			<?php
				//printf("Safest bar is %s <br />", $safest['name']);
				//printf("Most dangerous bar is %s <br />", $leastSafe['name']);
			?>
		</strong>
		<footer>
			<small>Made by Pawe&lstrok; Szczurko and Kevin Albertson</small>
		</footer>
	</div>
	<!-- google maps stuff -->
	    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBfKJWAEA2dIy33v-b074fvcnE--TSxi1U&v=3.exp&sensor=false&libraries=visualization"></script>
	    <script>
			var map, pointarray, heatmap;

			var offenderData = [
			<?php
			  $results = mysqli_query($cxn, "SELECT * FROM SexOffender, Drinker WHERE SexOffender.name = Drinker.name");
			  $first = true;
			  while($row = mysqli_fetch_assoc($results)){
			    if(!$first) echo ",";
			    else $first = false;
			    echo "new google.maps.LatLng(" . $row['latitude'] . "," . $row['longitude'] .")";
			  }
			?>

			];

			function initialize() {
			  var mapOptions = {
			    zoom: 8,
			    center: new google.maps.LatLng(40.207721, -74.635620),
			    mapTypeId: google.maps.MapTypeId.ROADMAP
			  };

			  map = new google.maps.Map(document.getElementById('map-canvas'),
			      mapOptions);

			  var pointArray = new google.maps.MVCArray(offenderData);

			  heatmap = new google.maps.visualization.HeatmapLayer({
			    data: pointArray
			  });

			  heatmap.setMap(map);
			}

			function changeGradient() {
			  var gradient = [
			    'rgba(0, 255, 255, 0)',
			    'rgba(0, 255, 255, 1)',
			    'rgba(0, 191, 255, 1)',
			    'rgba(0, 127, 255, 1)',
			    'rgba(0, 63, 255, 1)',
			    'rgba(0, 0, 255, 1)',
			    'rgba(0, 0, 223, 1)',
			    'rgba(0, 0, 191, 1)',
			    'rgba(0, 0, 159, 1)',
			    'rgba(0, 0, 127, 1)',
			    'rgba(63, 0, 91, 1)',
			    'rgba(127, 0, 63, 1)',
			    'rgba(191, 0, 31, 1)',
			    'rgba(255, 0, 0, 1)'
			  ]
			  heatmap.set('gradient', heatmap.get('gradient') ? null : gradient);
			}
			google.maps.event.addDomListener(window, 'load', initialize);

	    </script>
</body>
</html>
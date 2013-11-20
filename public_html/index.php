<?php 
	require_once("settings.php");
	$PG_NAME = "home";
	require_once("res/top.php"); //header, navigation, opening tags etc.
?>
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
		<table class="niceTable" cellspacing="0">
			<thead>
				<tr>
					<th class="barName">Bar Name</th>
					<th>Safety Rating*</th>
				</tr>
			</thead>
			<tbody>
				<?php
					//Select the most dangerous bars of the month
					$results = getAllRatings(true);
					$i = 10;
					while($i > 0 && $row = mysqli_fetch_assoc($results)){
						printf("<tr><td><a href='bar.php?bar=%s' title='View bar'>%s</a></td><td>%.1f</td></tr>", urlencode(trim($row['name'])), trim($row['name']), $row['rating']);
						$i--;
					}
				?>
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
	    mapTypeId: google.maps.MapTypeId.ROADMAP,
	    mapTypeControl: false
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

<?php require_once("res/bottom.php"); ?>
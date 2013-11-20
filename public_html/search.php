<?php
require_once("settings.php");
$PG_NAME='search';
require_once("res/top.php");

$searchType = "location";
$barName = "";
$location = "";
$lon = -1;
$lat = -1;
if(isset($_GET['bname']) && isset($_GET['location'])){
	$barName = trim($_GET['bname']); //extra space since data has it
	$location = trim($_GET['location']);
	if($location != "" && $_GET['location'] != 'Search by location (in NJ)'){
		if(!stristr($location, "NJ") && !stristr($location, "New Jersey")){
			$location .= ",NJ";
		}
		//search by location
		$results = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=" . urlencode($location));
		$results = json_decode($results, true);
		$lon = $results["results"][0]["geometry"]["location"]["lng"];
		$lat = $results["results"][0]["geometry"]["location"]["lat"];
		//only select top 10
		$q = sprintf("SELECT name, longitude, latitude, SQRT(POW(longitude - %f, 2) + POW(latitude - %f, 2)) AS dist, city FROM Bar ORDER BY dist LIMIT 10;", $lon, $lat);
		$results = mysqli_query($cxn, $q) or die("Could not find closest bars");
	}
	else if($barName != "" && $barName != "Search by bar name"){
		//search by bar
		$searchType = "barname";
		$terms = implode("%", explode(" ", $barName));
		$q = "SELECT name, city, longitude, latitude FROM Bar WHERE name LIKE '%" . $terms . "%' LIMIT 10";
		$results = mysqli_query($cxn, $q) or die("Could not find bars");
	}
	else{
		niceDie("Nothing submitted");
	}
}
else{
	niceDie("Nothing submitted");
}

?>
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
		<h4>Locations of Bars</h4>
		<div id="map-canvas"></div>
	</div>
	<div class="right50">
		<?php
			if($searchType == "location"){
				printf("<h4>Search by location (%s)</h4>", $location);
			}
			else if($searchType == "barname"){
				printf("<h4>Search by bar (%s)</h4>", $barName);
			}
		?>
		<p>Click the bar names to find out more information about the safety of the bar</p>
		<?php
			if(mysqli_num_rows($results) == 0){
				echo "No bars were found";
			}
			echo "<table class='niceTable' cellspacing='0'><thead><tr><th class='barName'>Bar Name</th><th>City</th>";
			if($searchType == "location"){
				echo "<th>Distance*</th>";
			}
			echo "</tr></thead><tbody>";
			while($row = mysqli_fetch_assoc($results)){
				//get the safety rating for each bar, plot on map
				printf("<tr><td><a href='bar.php?bar=%s' title='View bar'>%s</a></td><td>%s</td>" , urlencode($row['name']), $row['name'], $row['city']);
				if($searchType == "location"){
					printf("<td>%.2f</td>", ($row['dist'] * 100));
				}
				printf("</tr>");
			}
			echo "</tbody></table>";
		?>
		<br/>
		<small>* Distance is approximate and in kilometers</small>
	</div>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBfKJWAEA2dIy33v-b074fvcnE--TSxi1U&v=3.exp&sensor=false"></script>
<script>
	var map;
	function initialize() {
	  var mapOptions = {
	    <?php
	    if($searchType == "location"){
	    	echo "center: new google.maps.LatLng(" . $lat . ", " . $lon . "), zoom: 12,";
	    }
	    else{
	    	echo "center: new google.maps.LatLng(40.207721, -74.635620), zoom: 8,";
		}
		?>
	    mapTypeId: google.maps.MapTypeId.ROADMAP,
	    mapTypeControl: false
	  };

	  map = new google.maps.Map(document.getElementById('map-canvas'),
	      mapOptions);
	  var infowindow = new google.maps.InfoWindow({ content: ''});
	  <?php
	  mysqli_data_seek($results, 0);
	  $i = 0;
	  //this is pretty hacky
	  while($row = mysqli_fetch_assoc($results)){
	    echo "var marker".$i." = new google.maps.Marker({ position: new google.maps.LatLng(" . $row['latitude'] . "," . $row['longitude'] ."), map: map, title: '" . $row['name'] . "'});\n";
	    if($i == 0){
	    	echo "infowindow.setContent('<a href=\"bar.php?bar=". $row['name'] ."\" title=\"View bar\">" . $row['name'] . "</a>'); infowindow.open(map,marker".$i.");";
	    }
	   
		echo "google.maps.event.addListener(marker".$i.", 'click', function() { infowindow.setContent('<a href=\"bar.php?bar=". $row['name'] ."\" title=\"View bar\">" . $row['name'] . "</a>'); infowindow.open(map,marker".$i.");});";
	    $i++;
	  }
	?>

	}

	google.maps.event.addDomListener(window, 'load', initialize);

</script>
<?php
require_once("res/bottom.php");
?>

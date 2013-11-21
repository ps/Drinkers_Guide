<?php
require_once("settings.php");
$PG_NAME = "predator";
require_once("res/top.php");
if(!isset($_GET['name'])){
	niceDie("No predator name specified.");
}
?>

<?php
$predator_name = mysql_escape_string( trim($_GET['name']));
$predator_img = "";
$predator_history = NULL; //result object
$dangerRating = 0;
$lat = -1;
$lon = -1;
$radius = 0;
$city = "";
$street = "";

//Show this predator's information, and show a strike-zone map (also possibly include his location)

$q = "SELECT so.name AS name, d.gender AS gender, d.city as city, d.address as street, COUNT(*) as numOffenses, d.latitude AS lat, d.longitude AS lon, MAX(SQRT(POW(d.latitude - b.latitude,2) + POW(d.longitude - b.longitude, 2))) AS radius FROM SexOffender so, Drinker d, Frequents f, Bar b WHERE so.name = f.drinker AND f.bar = b.name AND d.name=so.name AND so.name='" . $predator_name . "' GROUP BY so.name";
$results = mysql_query( $q) or die("Could not get sex offender's information");

if(mysql_num_rows($results) == 0){
	die("No sex offender found");
}
$row = mysql_fetch_assoc($results);

$street = $row["street"];
$city = $row['city'];
$lat = $row['lat'];
$lon = $row['lon'];
$radius = $row['radius'] * 90000;

$imgFile = "res/images_females.txt";
if($row['gender'] == 'M'){
	$imgFile = "res/images_males.txt";
}
$contents = file_get_contents($imgFile);
$rows = explode("\n", $contents);
$contentsChanged = false;
for($i = 0; $i < sizeof($rows); $i++){
	$r = $rows[$i];
	$parts = explode("$", $r);
	if(sizeof($parts) == 2){
		
		if($parts[0] == $predator_name){
			$predator_img = $parts[1];
			break;
		}
	}
	else{
		//add to this file
		$rows[$i] = $predator_name . "$" . $r;
		$contentsChanged = true;
		$predator_img = $r;
		break;
	}
}
if($contentsChanged){
	$FILE = fopen($imgFile, "w");
	fwrite($FILE, implode("\n", $rows));
	fclose($FILE);
}

$q = sprintf("SELECT s.victim as victim, s.dateOfCrime as dateOfCrime, d.gender as victimGender FROM SexOffender s, Drinker d WHERE s.name = '%s' and s.victim = d.name", $predator_name);
$history = mysql_query( $q) or die("Could not get history");



/*
danger rating:
- # of offences
- whether he/she bi more dangerous
- illegal beers
*/
$dangerRank = mysql_num_rows($history);
$sexes = array("M" => 0, "F" => 0);
$histText = "<ul class='marginFix'>";
while($row = mysql_fetch_assoc($history)){
	$sexes[$row['victimGender']] = 1;
	$histText .= sprintf("<li><span style='width:150px; display:inline-block;'>%s</span><b>(%s)</b> </li>", $row['victim'], $row['dateOfCrime']);
}
$histText .= "</ul>";

if($sexes["M"] == 1 && $sexes["F"] == 1){
	$dangerRank += 3;
}

if($dangerRank > 10){
	$dangerRank = 10;
}


?>
<div class="columns cf">
  <div class="left50">
    <?php
    printf("<img style='border:1px black solid' width='150' src='%s' />", $predator_img);
    printf("<h2>%s <div style='position: relative; top: -2px;' class='ratingCircle r%d'>%d</div></h2>", $predator_name, 10-$dangerRank, $dangerRank);
    printf("<p class='n'>%s, %s, NJ</p>", $street, $city);
    printf("<p class='n'>Danger Rank: %d</p>" , $dangerRank);
    echo "<br/><h3>Previous Offenses</h3>";
    echo $histText;
    ?>
  </div>
  <div class="right50">
    <h4>Danger Zone</h4>
    <div id="map-canvas"></div>
  </div>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBfKJWAEA2dIy33v-b074fvcnE--TSxi1U&v=3.exp&sensor=false&libraries=visualization"></script>
 <script>
var map, pointarray, heatmap;

function initialize() {
  var mapOptions = {
    zoom: 8,
    center: new google.maps.LatLng(40.207721, -74.635620),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };

  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);
  <?php
    //the radius must be in meters
    //approximation for scaling http://geography.about.com/library/faq/blqzdistancedegree.htm
    // ~ 100km
    //so this only looks good for 1 person at a time
      echo "new google.maps.Circle({
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 1,
        fillColor: '#FF0000',
        fillOpacity: .3, 
        map: map,
        center: new google.maps.LatLng(" . $lat . "," . $lon ."),
        radius: " . $radius . "
        });";
      
  ?>
}



google.maps.event.addDomListener(window, 'load', initialize);

    </script>

<?php require_once("res/bottom.php"); ?>
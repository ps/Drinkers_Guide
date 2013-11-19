<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Heatmaps</title>
    <style>
      * {
        margin: 0px;
        padding: 0px
      }
       #map-canvas{
          width: 400px;
          height: 600px;
       }
    </style>
     <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBfKJWAEA2dIy33v-b074fvcnE--TSxi1U&v=3.exp&sensor=false&libraries=visualization"></script>
  
  </head>

  <body>
<?php
require_once("settings.php");
if(!isset($_GET['name'])){
	die("No predator name specified.");
}
$predator_name = mysqli_escape_string($cxn, trim($_GET['name']));
$predator_img = "";
$predator_history = NULL; //result object
$dangerRating = 0;
$lat = -1;
$lon = -1;
$radius = 0;
$city = "";

//Show this predator's information, and show a strike-zone map (also possibly include his location)

$q = "SELECT so.name AS name, d.gender AS gender, d.city as city, COUNT(*) as numOffenses, d.latitude AS lat, d.longitude AS lon, MAX(SQRT(POW(d.latitude - b.latitude,2) + POW(d.longitude - b.longitude, 2))) AS radius FROM SexOffender so, Drinker d, Frequents f, Bar b WHERE so.name = f.drinker AND f.bar = b.name AND d.name=so.name AND so.name='" . $predator_name . "' GROUP BY so.name";
$results = mysqli_query($cxn, $q) or die("Could not get sex offender's information");
$row = mysqli_fetch_assoc($results);

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
$predator_img = $rows[rand(0, sizeof($rows))];

$q = sprintf("SELECT s.victim as victim, s.dateOfCrime as dateOfCrime, d.gender as victimGender FROM SexOffender s, Drinker d WHERE s.name = '%s' and s.victim = d.name", $predator_name);
$history = mysqli_query($cxn, $q) or die("Could not get history");

printf("<img width='150' src='%s' />", $predator_img);
printf("<h2>%s</h2>", $predator_name);
printf("<p>From %s</p>", $city);

/*
danger rating:
- # of offences
- whether he/she bi more dangerous
- illegal beers
*/
$dangerRank = mysqli_num_rows($history);
printf("<h2>Previous offenses</h2>");
$sexes = array("M" => 0, "F" => 0);
while($row = mysqli_fetch_assoc($history)){
	$sexes[$row['victimGender']] = 1;
	printf("%s | %s <br/>", $row['victim'], $row['dateOfCrime']);
}

if($sexes["M"] == 1 && $sexes["F"] == 1){
	$dangerRank += 3;
}

if($dangerRank > 10){
	$dangerRank = 10;
}

printf("Danger Rank: %d" , $dangerRank);
?>

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
        fillOpacity: .1, 
        map: map,
        center: new google.maps.LatLng(" . $lat . "," . $lon ."),
        radius: " . $radius . "
        });";
      
  ?>
}



google.maps.event.addDomListener(window, 'load', initialize);

    </script>
   
    <div id="map-canvas"></div>
  </body>
</html>
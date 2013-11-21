<?php
require_once("settings.php");
?>
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
    <script>
var map, pointarray, heatmap;

var offenderData = [
<?php
  $results = mysql_query( "SELECT * FROM SexOffender, Drinker WHERE SexOffender.name = Drinker.name");
  $first = true;
  while($row = mysql_fetch_assoc($results)){
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

function toggleHeatmap() {
  heatmap.setMap(heatmap.getMap() ? null : map);
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

function changeRadius() {
  heatmap.set('radius', heatmap.get('radius') ? null : 20);
}

function changeOpacity() {
  heatmap.set('opacity', heatmap.get('opacity') ? null : 0.2);
}

google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>

  <body>
    <div id="map-canvas"></div>
  </body>
</html>
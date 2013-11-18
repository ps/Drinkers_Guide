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

function initialize() {
  var mapOptions = {
    zoom: 8,
    center: new google.maps.LatLng(40.207721, -74.635620),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };

  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);
  <?php
    $results = mysqli_query($cxn, "SELECT so.name AS name, d.latitude AS lat, d.longitude AS lon, MAX(SQRT(POW(d.latitude - b.latitude,2) + POW(d.longitude - b.longitude, 2))) AS radius FROM SexOffender so, Drinker d, Frequents f, Bar b WHERE so.name = f.drinker AND f.bar = b.name AND d.name=so.name GROUP BY so.name LIMIT 10;");
    $first = true;
    //the radius must be in meters
    //approximation for scaling http://geography.about.com/library/faq/blqzdistancedegree.htm
    // ~ 100km
    //so this only looks good for 1 person at a time
      while($row = mysqli_fetch_assoc($results)){
      echo "new google.maps.Circle({
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 1,
        fillColor: '#FF0000',
        fillOpacity: .1, 
        map: map,
        center: new google.maps.LatLng(" . $row['lat'] . "," . $row['lon'] ."),
        radius: (" . $row['radius'] . " * 100000)
        });";
      }
  ?>
}



google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>

  <body>
    <div id="map-canvas"></div>
  </body>
</html>
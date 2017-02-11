<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Localisation et prévision météorologique</title>
    <style type="text/css">
      html, body { height: 100%; margin: 0; padding: 0; }
      #map {
        max-height: 650px;
        height: 100%;
      }
    </style>
  </head>
  <body>
    <div id="map"></div>

    <script type="text/javascript" src="oXHR.js"></script>
    <script type="text/javascript" src="app.weather.js"></script>
    <script type="text/javascript" src="app.map.js"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAdQ6ZVlCQpRrQsyfhVDRGrHxreUkIKgOw&callback=getWeather"></script>
  </body>
</html>
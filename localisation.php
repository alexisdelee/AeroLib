<?php
  require_once("init.php");
  require_once("controllers/Authentification.php");
  require_once("nav.php");
  require_once("popup.php");
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Localisation et prévision météorologique</title>
    <link rel="stylesheet" type="text/css" href="style/localisation.css">
    <link rel="icon" type="image/png" href="res/logo.png">
  </head>
  <body>
    <div id="map"></div>

    <section>
      <center>
        <p>
          <?php
            if($router->permission == 2) {
              echo "<a href=\"weatherService.php\">Réactualiser manuellement le service météo</a><br><br>";
            }
          ?>
          Cliquez sur l'icone rouge pour avoir accès aux prévisions, ou naviguez sur la carte.<br>
          <i>Le service météorologique se remet à jour toutes les heures automatiquement.</i>
        </p>
      </center>
    </section>

    <script type="text/javascript" src="controllers/oXHR.js"></script>
    <script type="text/javascript" src="app.weather.js"></script>
    <script type="text/javascript" src="app.map.js"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo Authentification::api('googlemap'); ?>&callback=getWeather"></script>
  </body>
</html>
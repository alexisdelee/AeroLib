<?php
  require_once("init.php");
  require_once("nav.php");
  require_once("popup.php");
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Activités aéroclub</title>
    <link rel="stylesheet" type="text/css" href="style/popup.css">
    <link rel="stylesheet" type="text/css" href="style/escale.css">
    <link rel="icon" type="image/png" href="res/logo.png">
    <style type="text/css">
      .min-select {
        padding: 3px;
      }

      input[type="text"] {
        width: 100px;
        padding: 4px;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <?php if(isset($_GET["prestation"])) { ?>
        <article>
          <center>
            <p>
              <?php
                if(!isset($_GET["type"]) || $_GET["type"] != "confirmation") {
                  echo "A quelle date souhaitez-vous avoir accès à la prestation ? <input id=\"date\" type=\"text\" style=\"width: 150px;\" placeholder=\"10/04/2017 13:13\"><br><br>";
                }
              ?>

              <button id="accept">Ajouter au panier</button>
              <button id="cancel" onclick="window.location.href = 'aeroclub.php'">Annuler</button>
            </p>
          </center>
        </article>
      <?php } else { ?>
        <ul class="accordion">
          <li class="tabs open">
            <div class="prestations simple_services">
              <a title="Services simples" href="#simple_services">Services simples</a>
            </div>
            <div class="paragraph">
              <h1 style="margin-top: -20px;">Services simples</h1>

              <select class="min-select">
                <option value="defaut">Activités aéroclub</option>
                <?php
                  $results = $manager->getAll("
                    SELECT title FROM `activity`;
                  ");

                  foreach($results as $result) {
                    echo "<option value=\"" . urlencode(utf8_encode($result["title"])) . "\">" . ucfirst(utf8_encode($result["title"])) . "</option>";
                  }
                ?>
              </select>

              <input type="text" style="width: 160px;" placeholder="Durée prestation (minute)"><br><br>
              <input type="checkbox" id="weight"><label for="weight"> Pesez-vous plus de 40kg ?</label>
              <button class="send" data-prestation="simple_services" data-href="<?php echo $router->rewriteUrl("prestation", "simple_service"); ?>">Valider</button>
            </div>
          </li>
        </ul>
      <?php } ?>
    </div>

    <script type="text/javascript" src="libs/moment/moment.js"></script>
    <script type="text/javascript" src="controllers/MomentUtils.js"></script>
    <script type="text/javascript" src="controllers/oXHR.js"></script>
    <script type="text/javascript" src="controllers/Request.js"></script>
    <script type="text/javascript" src="app.popup.js"></script>
    <script type="text/javascript" src="scenario_aeroclub.js"></script>
    <?php if(isset($_SESSION["prestation"])) { ?>
      <script type="text/javascript">
        popup.manager.open("Cette prestation a été ajoutée à votre panier (pour un total de " + <?php echo ($_SESSION["prestation"]["cost"] + $_SESSION["prestation"]["tva"]); ?> + "€ dont " + <?php echo $_SESSION["prestation"]["tva"]; ?> + "€ de TVA).");
      </script>
    <?php
        unset($_SESSION["prestation"]);
      }
    ?>
  </body>
</html>
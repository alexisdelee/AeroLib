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
          <li class="tabs">
            <div class="prestations simple_services">
              <a title="Services simples" href="#simple_services">Services simples</a>
            </div>
            <div class="paragraph">
              <h2 style="margin-top: -20px;">Services simples</h2><br>

              <select class="min-select">
                <option value="defaut">Activités aéroclub</option>
                <?php
                  $results = $manager->getAll("
                    SELECT title, age FROM `activity`
                    WHERE formation <> 1
                  ");

                  foreach($results as $result) {
                    echo "<option value=\"" . urlencode(utf8_encode($result["title"])) . "\">" . ucfirst(utf8_encode($result["title"])) . "</option>";
                  }
                ?>
              </select>

              <input type="text" style="width: 187px;" placeholder="Durée prestation (en minutes)"><br><br>
              
              <small>
                <input style="position: relative; top: 2px;" type="checkbox" id="limit"><label for="limit"> j'ai plus de <?php echo (empty($results) ? 16 : $results[0]["age"]); ?> ans</label>
              </small>

              <button class="send" data-prestation="simple_services" data-href="<?php echo $router->rewriteUrl("prestation", "simple_service"); ?>">Valider</button>
            </div>
          </li>
          <li class="tabs open">
            <div class="prestations extra_service">
              <a title="Service extra" href="#extra_service">Service extra</a>
            </div>
            <div class="paragraph">
              <h2 style="margin-top: -30px;">Formation au brevet de pilote</h2><br>

              <select class="min-select" id="formation">
                <?php
                  $results = $manager->getAll("
                    SELECT title, age, description, cost, tva FROM `activity`
                    WHERE formation = 1
                  ");

                  foreach($results as $result) {
                    echo "<option data-information=\"" . utf8_encode($result["description"]) . "\" value=\"" . urlencode(utf8_encode($result["title"])) . "\">" . ucfirst(utf8_encode($result["title"])) . " [" . ($result["cost"] + $result["tva"]) . "€]" . "</option>";
                  }
                ?>
              </select><br><br>

              <small>
                <input type="checkbox" id="club"><label style="position: relative; top: -2px;" for="club"> je suis membre d'un autre club</label><br>
                <input type="checkbox" id="age_legal"><label style="position: relative; top: -2px;" for="age_legal"> j'ai plus de 21 ans</label><br>
                <input type="checkbox" id="revue"><label style="position: relative; top: -2px;" for="revue"> je souscris à la revue mensuelle "Info pilote"</label>
              </small>

              <button id="confirm">Plus d'informations</button>
              <button class="send" data-prestation="extra_service" data-href="<?php echo $router->rewriteUrl("prestation", "extra_service"); ?>">Valider</button>
            </div>
          </li>
        </ul>
      <?php } ?>
    </div>

    <script type="text/javascript" src="libs/moment/moment.js"></script>
    <script type="text/javascript" src="libs/moment/moment-ferie-fr.js"></script>
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
    <script type="text/javascript">
      let formationContainer = document.querySelector("#formation");
      let infoContainer = document.querySelector("#confirm");

      /* bouton plus d'informations */

      if(infoContainer != null) {
        infoContainer.addEventListener("click", () => {
          let option = formationContainer.selectedOptions[0].dataset.information;
          if(option != undefined) {
            popup.manager.open("<span>" + option.replace(/\n/g, "<br>") + "</span>");
          }
        });
      }
    </script>
  </body>
</html>
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
              <?php if(!isset($_GET["type"]) || $_GET["type"] != "confirmation") { ?>
                Pour qui est cette prestation ? <?php echo isset($_SESSION["name"]) ? "(par défaut <strong>" . $_SESSION["name"] . "</strong>)" : ""; ?><br>
                <input style="position: relative; top: 1px;" type="checkbox" id="other"><label for="other"> <input id="other_input" style="height: 25px; width: 140px; border: none; border-bottom: 2px solid black;" type="text" placeholder="nom si autre personne"></label>
                agé(e) de <input style="width: 27px; border: 2px solid #222222; border-radius: 2px;" type="text" id="age_other" placeholder="18"> ans<br><br><br>

                A quelle date souhaitez-vous avoir accès à la prestation ? <input id="date" type="text" style="width: 115px; border: none; border-bottom: 2px solid #222222;" placeholder="10/04/2017 13:13"><br><br>
              <?php } ?>

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
              <h2>Services simples</h2><br>

              <select class="min-select">
                <?php
                  $results = $manager->getAll("
                    SELECT title, age, cost, tva FROM `activity`
                    WHERE formation <> 1
                  ");

                  foreach($results as $result) {
                    echo "<option value=\"" . urlencode(utf8_encode($result["title"])) . "\">" . ucfirst(utf8_encode($result["title"])) . " [" . ($result["cost"] + $result["tva"]) . "€/h]" . "</option>";
                  }
                ?>
              </select>

              <input type="text" style="width: 35px; border: 2px solid #222; border-radius: 2px;" placeholder="120"> minutes (durée de la prestation)<br><br>

              <button class="send" data-prestation="simple_services" data-href="<?php echo $router->rewriteUrl("prestation", "simple_service"); ?>">Valider</button>
            </div>
          </li>
          <li class="tabs">
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
    <?php
      if(isset($_SESSION["prestation"]["frais"])) {
        $annexe = array_map(function($value) {
          return "<li>" . $value . "</li>";
        }, $_SESSION["prestation"]["frais"]);

        $annexe = implode("", $annexe);
      }
    ?>
      <script type="text/javascript">
        popup.manager.open("Cette prestation a été ajoutée à votre panier (pour un total de " + <?php echo ($_SESSION["prestation"]["cost"] + $_SESSION["prestation"]["tva"]); ?> + "€ dont " + <?php echo $_SESSION["prestation"]["tva"]; ?> + "€ de TVA)."
          + "<?php echo isset($annexe) ? "<br><br><ul>Des frais supplémentaires vous ont été ajoutés:<br>" . $annexe . "</ul>" : ""; ?>");
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

      /* nom autre personne */
      let other_input = document.querySelector("#other_input");
      let age_other = document.querySelector("#age_other");
      let other = document.querySelector("#other");
      let fill = [false, "<?php echo isset($_SESSION["name"]) ? $_SESSION["name"] : ""; ?>", <?php echo isset($_SESSION["name"]) ? $_SESSION["age"] : 0; ?>];

      if(other_input != null) {
        other_input.addEventListener("keyup", (e) => {
          if(e.target.value.length) {
            if(!fill[0]) {
              fill[2] = 0;
            }

            other.checked = true;
            fill[0] = true;
            fill[1] = e.target.value;
          } else {
            other.checked = false;
            fill[0] = false;
          }
        });
      }

      if(other != null) {
        other.addEventListener("change", (e) => {
          if(e.target.checked && !fill[0]) {
            e.target.checked = false;
            other_input.focus();
          } else if(!e.target.checked && fill[0]) {
            e.target.checked = true;
          }
        });
      }

      if(age_other != null) {
        age_other.addEventListener("keyup", (e) => {
          let value = e.target.value;

          if(!isNaN(value)) {
            fill[2] = parseInt(value);
          } else {
            fill[2] = 0;
          }
        });
      }
    </script>
  </body>
</html>
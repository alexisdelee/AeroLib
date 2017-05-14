<?php
  require_once("init.php");
  require_once("nav.php");
  require_once("popup.php");
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Services clients</title>
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
      <?php if(isset($_GET["prestation"], $_GET["matricule"])) { ?>
        <section style="position: relative; top: -100px;">
          <p>
            <center>
              Vous avez sélectionné l'avion de matricule <?php echo $_GET["matricule"]; ?>.<br>
              <small><a title="changer de matricule" href="#" onclick="changeMatricule(); return false;">changer de matricule</a></small>
            </center>
          </p>
        </section>

        <article>
          <center>
            <p>
              <?php if(!isset($_GET["type"]) || $_GET["type"] != "confirmation") { ?>
                <span>A quelle date souhaitez-vous avoir accès à la prestation ? </span><input id="date" type="text" style="border: none; border-bottom: 2px solid #222; width: 113px;" placeholder="10/04/2017 13:13" autofocus=""><br><br>
              <?php } ?>

              <button id="accept">Ajouter au panier</button>
              <button id="cancel" onclick="window.location.href = 'escale.php'">Annuler</button>
            </p>
          </center>
        </article>
      <?php } else if(isset($_GET["prestation"])) { ?>
        <section id="plane" style="position: relative; top: -100px;">
            <p>
              Choix d'un avion :<br>
              &emsp;<input type="radio" name="plane" id="select"><label for="select"> sélectionner un avion pré-enregisté</label>

              <?php
                $manager = PDOUtils::getSharedInstance();
                $matricules = $manager->getAll("
                  SELECT matricule
                  FROM `plane`
                    LEFT JOIN `user` ON plane.idUser = user.idUser
                  WHERE user.email = ?
                ", [$_SESSION["email"]]);

                echo "<select id=\"matricules\">";
                if(empty($matricules)) {
                  echo "<option>aucun avion en réserve</option>";
                } else {
                  echo "<option>" . count($matricules) . " matricule(s) enregistré(s)</option>";
                  foreach($matricules as $matricule) {
                    echo "<option>" . utf8_encode($matricule["matricule"]) . "</option>";
                  }
                }
                echo "</select><br>";
              ?>

              &emsp;<input type="radio" name="plane" id="register"><label for="register"> enregistrer un nouvel avion</label><br><br>
              <div id="subscribe" style="display: none;">
                <table>
                  <tr>
                    <th>Matricule</th>
                    <th>Longueur</th>
                    <th>Envergure</th>
                    <th>Masse maximum</th>
                    <th>Modèle</th>
                    <th>Groupe acoustique</th>
                    <th></th>
                  </tr>
                  <tr>
                    <td><input type="text" placeholder="8 caractères"></td>
                    <td><input type="text" placeholder="en mètre"></td>
                    <td><input type="text" placeholder="en mètre"></td>
                    <td><input type="text" placeholder="en kg"></td>
                    <td>
                      <select class="min-select">
                        <?php
                          $result = $manager->getAll("SELECT typeModel FROM `model`");
                          foreach($result as $data) {
                            echo "<option>" . utf8_encode($data["typeModel"]) . "</option>";
                          }
                        ?>
                      </select>
                    </td>
                    <td>
                      <select class="min-select">
                        <?php
                          $result = $manager->getAll("SELECT groupAcoustic FROM `acoustic`");
                          foreach($result as $data) {
                            echo "<option>" . $data["groupAcoustic"] . "</option>";
                          }
                        ?>
                      </select>
                    </td>
                    <td><button id="newplane">enregistrer</button></td>
                  </tr>
                </table>
              </div>
            </p>
          </section>
      <?php } else { ?>
        <ul class="accordion">
          <li class="tabs open">
            <div class="prestations landing">
               <a title="Atterrissage" href="#area">Atterrissage</a>
            </div>
            <div class="paragraph">
              <h2 style="margin-top: -40px;">Atterrissage</h2><br>

              <select class="min-select" style="margin-top: -10px;">
                <?php
                  $result = $manager->getAll("
                    SELECT DISTINCT timetable
                    FROM `landing`
                    WHERE timetable <> \"Week-end/JF\"
                  ");

                  foreach($result as $data) {
                    echo "<option>" . utf8_encode($data["timetable"]) . "</option>";
                  }
                ?>
              </select>

              <h3 style="margin-top: 5px; margin-bottom: 10px;">Stationnement</h3>

              <p>
                <select class="min-select">
                  <optgroup label="Intérieur">
                    <?php
                      $result = $manager->getAll("
                        SELECT DISTINCT timetable
                        FROM `category`
                      ");

                      foreach($result as $data) {
                        echo "<option value=\"interieur\">" . utf8_encode($data["timetable"]) . "</option>";
                      }
                    ?>
                  </optgroup>
                  <optgroup label="Extérieur">
                    <option value="exterieur">Tarif basic</option>
                  </optgroup>
                </select>

                <input type="text" placeholder="15" style="width: 34px; border: 2px solid #222; border-radius: 2px;"> jour(s)
                <button id="confirm">Confirmer atterrissage</button>
                <button class="send" data-prestation="atterrissage" data-href="<?php echo $router->rewriteUrl("prestation", "atterrissage"); ?>">Valider</button>
              </p>
            </div>
          </li>
          <li class="tabs">
            <div class="prestations avitaillement">
              <a title="Avitaillement" href="#avitaillement">Avitaillement</a>
            </div>
            <div class="paragraph">
              <h2 style="margin-top: 10px;">Avitaillement</h2><br>

              <p>
                <select class="min-select">
                  <?php
                    $manager = PDOUtils::getSharedInstance();
                    $result = $manager->getAll("SELECT product, costReservoir, tvaReservoir FROM `reservoir`");

                    foreach($result as $data) {
                      echo "<option value=\"" . $data["product"] . "\">" . $data["product"] . " [" . ($data["costReservoir"] + $data["tvaReservoir"]) . "€/L]" . "</option>";
                    }
                  ?>
                </select>

                <input type="text" placeholder="1200" style="width: 42px; border: 2px solid #222; border-radius: 2px;"> litre(s)
                <button class="send" data-prestation="avitaillement" data-href="<?php echo $router->rewriteUrl("prestation", "avitaillement"); ?>">Valider</button>
              </p>
            </div>
          </li>
          <li class="tabs">
            <div class="prestations cleaning">
              <a title="Nettoyage" href="#cleaning">Nettoyage</a>
            </div>
            <div class="paragraph">
              <h2 style="margin-top: 10px;">Nettoyage</h2><br>

              <?php
                $result = $manager->getAll("
                  SELECT costCleaning, tvaCleaning
                  FROM `cleaning`
                ");

                if(!empty($result)) {
                  echo "<p>" . ($result[0]["costCleaning"] + $result[0]["tvaCleaning"]) . "€ par surface d'avion dont " . $result[0]["tvaCleaning"] . "€ de TVA.</p>";
                }
              ?>
              <button class="send" data-prestation="nettoyage" data-href="<?php echo $router->rewriteUrl("prestation", "nettoyage"); ?>">Valider</button>
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
    <script type="text/javascript" src="scenario.js"></script>
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
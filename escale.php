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
  </head>
  <body>
    <section>
      <center>
        <p style="margin: 0px auto;">
          <div id="container">
            <?php
              $manager = PDOUtils::getSharedInstance();
              $prestations = [
                [
                  "table" => "category",
                  "title" => "Atterrissage",
                  "path" => "landing.png"
                ],
                [
                  "table" => "reservoir",
                  "title" => "Avitaillement",
                  "path" => "reservoir.png"
                ],
                [
                  "table" => "area",
                  "title" => "Stationnement",
                  "path" => "area.png"
                ],
                [
                  "table" => "cleaning",
                  "title" => "Nettoyage",
                  "path" =>"cleaning.png"
                ]
              ];

              foreach($prestations as $prestation) {
                echo "<a onclick=\"choosePrestation(" . $prestation["table"] . "); return false;\" title=\"" . $prestation["title"] . "\" href=\"#" . $prestation["table"] . "\"><div class=\"prestations available\"><img style=\"width: 100%; height: 100%; opacity: 0.8\" src=\"res/prestations/" . $prestation["path"] . "\"></div></a>";
              }
            ?>
          </div>
        </p>
      </center>
    </section>

    <article id="prestations">
      <center>
        <p>
          <div class="prestation" id="category" style="display: none;">
            <!-- <select>
              <?php
                /* $data = $manager->getAll("SELECT idModel, typeModel FROM `model`");
                echo "<option value=\"default\">Type d'avion</option>";
                foreach($data as $value) {
                  echo "<option value=\"idModel\" data-id=\"" . $value["idModel"] . "\" data-nexttable=\"landing\">" . utf8_encode($value["typeModel"]) . "</option>";
                } */
              ?>
            </select>

            <select>
              <?php
                /* $data = $manager->getAll("SELECT idAcoustic, groupAcoustic FROM `acoustic`");
                echo "<option value=\"default\">Groupe acoustique</option>";
                foreach($data as $value) {
                  echo "<option value=\"idAcoustic\" data-id=\"" . $value["idAcoustic"] . "\">" . utf8_encode($value["groupAcoustic"]) . "</option>";
                } */
              ?>
            </select><br>

            <div class="row">
              <input name="form" id="date" type="text" placeholder="24-06-2017_13:14">
              <label for="date">Date</label>
            </div><br><br> -->

            <?php
              $data = $manager->getAll("SELECT idReceipt FROM `receipt` LEFT JOIN `user` ON receipt.idUser = user.idUser WHERE user.email = ? AND receipt.prestation = \"Atterrissage\"", [$_SESSION["email"]]);
              var_dump($data);
            ?>
          </div>
          <div class="prestation" id="reservoir" style="display: none;">
            <select>
              <?php
                $data = $manager->getAll("SELECT idReservoir, product FROM `reservoir`");
                echo "<option value=\"default\">Produit prétrolier</option>";
                foreach($data as $value) {
                  echo "<option value=\"idReservoir\" data-id=\"" . $value["idReservoir"] . "\">" . utf8_encode($value["product"]) . "</option>";
                }
              ?>
            </select><br>

            <div class="row">
              <input name="form" id="volume" type="text" placeholder="1200 (litres)">
              <label for="volume">Volume</label>
            </div><br><br>
          </div>

          <button style="display: none;">
            <div class="icon">
              <i class="fa fa-trash-o"></i>
              <i class="fa fa-question"></i>
              <i class="fa fa-check"></i>
            </div>
            <div class="text">
              <span>Valider</span>
            </div>
          </button>
        </p>
      </center>
    </article>

    <script type="text/javascript" src="controllers/oXHR.js"></script>
    <script type="text/javascript" src="app.popup.js"></script>
    <script type="text/javascript" src="escale.js"></script>
    <script type="text/javascript">
      let button = document.querySelector("button");
      let span = document.querySelector("button .text span");

      button.addEventListener("click", (e) => {
        if(e.target.classList.contains("confirm")) {
          e.target.className += " done";
          span.textContent = "Validé";

          // debug
          getValues();
          // debug
        } else {
          e.target.className += " confirm";
          span.textContent = "Êtes-vous sûr ?";
        }
      });

      // reset
      button.addEventListener("mouseout", (e) => {
        if(e.target.classList.contains("confirm") || e.target.classList.contains("done")) {
          setTimeout(() => {
            e.target.classList.remove("confirm");
            e.target.classList.remove("done");
            span.textContent = "Valider";
          }, 3000);
        }
      });
    </script>
  </body>
</html>
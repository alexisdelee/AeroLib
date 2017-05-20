<?php
  require_once("init.php");
  require_once("nav.php");
  require_once("popup.php");
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Compte</title>
    <link rel="icon" type="image/png" href="res/logo.png">
    <link rel="stylesheet" type="text/css" href="style/popup.css">
    <link rel="stylesheet" type="text/css" href="style/account.css">
  </head>
  <body>
    <?php if($router->permission == 3) { ?>
      <section>
        <center>
          <p>
            Sélectionnez le mois et l'année, puis validez pour générer une facture liée à cette date.
          </p>
        </center>
      </section>

      <span class="spinner">
        <span class="sub">-</span>
        <input type="number" min="1970" max="2100" value="2017" />
        <span class="add">+</span>
      </span>

      <div class="dropp">
        <div class="dropp-header"> <span class="dropp-header__title js-value"></span> <a href="#" id="month" class="dropp-header__btn js-dropp-action"><i class="icon"></i></a> </div>
        <div id="months" class="dropp-body"></div>
      </div>

      <div id="generate">Générer un fichier excel</div>

      <script type="text/javascript" src="controllers/Request.js"></script>
      <script type="text/javascript" src="controllers/Calendar.js"></script>
      <script type="text/javascript" src="export.js"></script>
    <?php } else { ?>
      <section>
        <center>
          <p>
            Ci-dessous est listé votre historique de factures.
          </p>
        </center>
      </section>

      <article id="historique">
        <center>
          <?php
            $manager = PDOUtils::getSharedInstance();
            $data = $manager->getAll("
              SELECT receipt.idReceipt, receipt.creation, receipt.totalCost, receipt.totalTva, receipt.isPaid, receipt.idAdministrative
              FROM `user` 
                LEFT JOIN `receipt` ON user.idUser = receipt.idUser 
              WHERE user.email = ? 
                AND receipt.creation <> 0
              ORDER BY idReceipt DESC
            ", [$_SESSION["email"]]);

            if(empty($data)) {
              echo "<i style=\"color: #A61835;\">Aucune facture disponible...</i>";
            } else {
              echo "<ul>";

              foreach($data as $value) {
                echo "<strong>[" . date("d/m/Y H:i:s", $value["creation"]) . "]</strong> : " . 
                  ($value["isPaid"] == 1 ? number_format(floatval($value["totalCost"]) + floatval($value["totalTva"]), 2, ",", " ") . " euro(s)"  : "(<a onclick=\"payNow(" . $value["idReceipt"] . "); return false;\" style=\"color: #000;\" href=\"#\">paiement immédiat</a>)") . 
                  ($value["idAdministrative"] != null ? "<small style=\"color: #A61835;\"> avec PENALITÉ pour retard de paiement</small>" : "") .
                " <a target=\"_blank\" data-id=\"" . $value["idReceipt"] . "\" href=\"phptopdf.php?id=" . $value["idReceipt"] . "\" title=\"Facture au format PDF\">format PDF</a><br>";
              }

              echo "</ul>";
            }
          ?>
        </center>
      </article>

      <section>
        <center>
          <p>
            Pour transférer de l'argent vers votre compte AEN, utilisez la zone prévue ci-dessous.<br>
            (<i>cliquez sur le bouton rouge pour annuler la transaction ou sur le bouton blanc pour la confirmer</i>)
          </p>
        </center>
      </section>

      <article>
        <center>
          <?php
            $manager = PDOUtils::getSharedInstance();
            $data = $manager->getAll("SELECT credit FROM `user` WHERE accesstoken = ?", [$_SESSION["accesstoken"]]);

            echo "<p>Crédit sur votre compte : " . number_format($data[0]["credit"], 2, ",", " ") . "€</p>";
          ?>
          <input type="text" id="credit">
        </center>
      </article>

      <section id="verification">
        <p>
          <form class="code_input">
            <center>
              <span>Entrez le code de confirmation reçu par mail :</span><br>
            </center>
            <?php
              for($input = 0; $input < 8; $input++) {
                echo "<input type=\"text\" class=\"" . (2 << $input) . "\">";
              }
            ?>
          </form>
        </p>
      </section>

      <script type="text/javascript" src="controllers/AutotabMagic.js"></script>
      <script type="text/javascript" src="controllers/Keypad.js"></script>
      <script type="text/javascript" src="controllers/oXHR.js"></script>
      <script type="text/javascript" src="controllers/Request.js"></script>
      <script type="text/javascript" src="controllers/oXHR.js"></script>
      <script type="text/javascript" src="app.popup.js"></script>
      <script type="text/javascript">
        // payer facture maintenant
        function payNow(id) {
          let request = new Request();
          request.post("service.php", "type=paid&receipt=" + id, (response) => {
            if(response === "ok") {
              window.location.reload();
            } else {
              popup.manager.open("<span style=\"color: #A61835\">" + response + "</span>");
            }
          });
        }

        // transère d'argent
        let target = document.querySelector("#credit");
        let amount = 0;

        let keypad = new Keypad();
        keypad.start(target);

        keypad.cancel = () => {
          keypad.start(target);
          target.value = "";
        };

        keypad.confirm = () => {
          let request = new XMLHttpRequest();
          request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200 && amount != 0){
              popup.manager.open("<span>Un mail avec le code de confirmation vous a été envoyé.</span>");
            }
          }

          amount = isNaN(parseInt(target.value)) ? 0 : parseInt(target.value);
          target.value = "";

          request.open("POST", "transaction.php");
          request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
          request.send("type=demand&amount=" + amount);
        };

        target.addEventListener("focus", () => {
          target.blur();
        });

        let register = new Autotab();
        register.listen(document.querySelector(".code_input"), 1, (keys, els) => {
          let request = new XMLHttpRequest();
          request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
              if(request.responseText == "true") {
                window.location.reload();
              } else {
                popup.manager.open("<span>Code invalide.</span");
              }
            }
          }

          request.open("POST", "transaction.php");
          request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
          request.send("type=verification&key=" + keys + "&amount=" + amount);

          register.clear(els);
        });
      </script>
    <?php } ?>
  </body>
</html>
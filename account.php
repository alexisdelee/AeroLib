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
          $data = $manager->getAll("SELECT idReceipt, prestation, dateOfDay, costReceipt, tvaReceipt FROM user LEFT JOIN receipt ON user.idUser = receipt.idUser WHERE user.email = ? ORDER BY dateOfDay DESC", [$_SESSION["email"]]);

          if($data[0]["idReceipt"] === null) {
            echo "<i style=\"color: #A61835;\">Aucune facture disponible...</i>";
          } else {
            echo "<ul>";

            foreach($data as $value) {
              echo "<input type=\"checkbox\" id=\"facture-" . $value["idReceipt"] . "\"><label for=\"facture-" . $value["idReceipt"] . "\"><strong>[" . date("Y-m-d H:i:s", $value["dateOfDay"]) . "]</strong> " . $value["prestation"] . " : " . (floatval($value["costReceipt"]) + floatval($value["tvaReceipt"])) . " euro(s) <a data-id=\"" . $value["idReceipt"] . "\" href=\"phptopdf.php?id=" . $value["idReceipt"] . "\" title=\"Facture au format PDF\">format PDF</a></label><br>";
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

          echo "<p>Crédit sur votre compte : " . $data[0]["credit"] . "€</p>";
        ?>
        <input type="text" id="credit">
      </center>
    </article>

    <section id="verification">
      <p>
        <form class="code_input">
          <center>
            <span>Entrez le code de vérification reçu par mail :</span><br>
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
    <script type="text/javascript" src="app.popup.js"></script>
    <script type="text/javascript">
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
            let span = document.createElement("span");
            span.textContent = "Un mail avec le code de confirmation vous a été envoyé.";
            popup.manager.open(span);
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
              let span = document.createElement("span");
              span.textContent = "Code invalide.";
              popup.manager.open(span);
            }
          }
        }

        request.open("POST", "transaction.php");
        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        request.send("type=verification&key=" + keys + "&amount=" + amount);

        register.clear(els);
      });
    </script>
  </body>
</html>
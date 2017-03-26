<?php
  require_once("init.php");
  require_once("nav.php");
  require_once("popup.php");
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Services administrateurs</title>
    <link rel="stylesheet" type="text/css" href="style/escale.css">
    <link rel="icon" type="image/png" href="res/logo.png">
    <style type="text/css">
      body { background: #EEEEEE; }
    </style>
  </head>
  <body>
    <?php
      if(isset($_SESSION["statut"]) && $_SESSION["statut"] == 2) {
        $options = [
          "default" => "Aucun service sélectionné",
          "acoustic" => "Groupes acoustiques",
          "administrative" => "Frais de dossiers",
          "area" => "Zones de stationnement",
          "category" => "Catégories redevance abris",
          "cleaning" => "Nettoyage",
          "indoorparking" => "Redevances abris",
          "landing" => "Redevances d'atterrissage",
          "model" => "Types avions",
          "outdoorparking" => "Redevances pour stationnement extérieur",
          "plane" => "Gestionnaire des avions",
          "prestation" => "Prestation",
          "receipt" => "Factures",
          "remittance" => "Redevances d'atterrissage pour hélicoptère ou ULM non basés",
          "reservoir" => "Produits pétroliers",
          "user" => "Utilisateur",
          "weather" => "Météo"
        ];

        echo "<section>";
        echo "<center>";
        echo "<p>";
        echo "<select>";

        foreach($options as $key => $value) {
          if($key === "default") {
            echo "<option value=\"" . $key . "\">" . $value . "</option>";
          } else {
            echo "<option value=\"" . $key . "\"> [" . $key . "] " . $value . "</option>";
          }
        }

        echo "</select>";
    ?>
            <div id="admin"></div>

            <a id="addRow" href="#">Ajouter une nouvelle ligne</a>
          </p>
        </center>
      </section>

      <script type="text/javascript" src="controllers/oXHR.js"></script>
      <script type="text/javascript">
        let table = null;

        document.querySelector("select").addEventListener("change", function(e){
          if(e.target.value != "default") {
            table = e.target.value;
            document.querySelector("#addRow").style.display = "inline-block";

            draw_db();
          }
        });

        document.querySelector("#addRow").addEventListener("click", function(e) {
          let inputs = document.querySelectorAll("input");

          insert(inputs);
          e.preventDefault();
        });

        function draw_db() {
          let request = new XMLHttpRequest();

          request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
              document.querySelector("#admin").innerHTML = request.responseText;

              let td = document.querySelectorAll(".data");
              td.forEach(function(els) {
                els.addEventListener("keydown", function(e) {
                  if(e.key.length === 1) { // detect a printable key
                    e.target.style.background = "#A61835";
                    e.target.style.color = "#1B1E24";
                    e.target.style.fontWeight = "bold";
                  } else if(e.keyCode == 13 || e.keyCode == 9) { // enter key or tab key
                    update(e.target);

                    e.target.style.background = "#EEEEEE";
                    e.target.style.color = "";
                    e.target.style.fontWeight = "normal";

                    if(e.keyCode == 13) {
                      e.target.blur();
                      window.getSelection().removeAllRanges();
                      e.preventDefault();
                    }
                  }
                });
              });
            }
          }

          request.open("POST", "administration.php");
          request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
          request.send("action=select&table=" + table);
        }

        function update(target) {
          let column = target.dataset.column;
          let value = target.textContent;
          let id = target.parentElement.firstChild.textContent;

          let request = new XMLHttpRequest();

          request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
              draw_db();
            }
          }

          request.open("POST", "administration.php");
          request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
          request.send("action=update&table=" + table + "&column=" + column + "&value=" + value + "&id=" + id);
        }

        function insert(inputs) {
          let columns = [];
          let values = [];

          inputs.forEach(function(els) {
            if(els.value.length > 0) {
              columns.push(els.dataset.column);
              values.push(els.value);
            }
          });

          let request = new XMLHttpRequest();

          request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
              if(columns.length > 0) {
                draw_db();
              }
            }
          }

          request.open("POST", "administration.php");
          request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
          request.send("action=insert&table=" + table + "&columns=" + columns.join("##") + "&values=" + values.join("##"));
        }
      </script>
    <?php } ?>
  </body>
</html>

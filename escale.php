<?php
  session_start();

  require_once("class.logPDO.php");
  require_once("class.user.php");
  require_once("nav.php");

  $user = new User();
  if(!$user->isConnected()) {
    header("Location: index.php");
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Services clients</title>
    <link rel="stylesheet" type="text/css" href="style/escale.css">
    <link rel="icon" type="image/png" href="res/logo.png">
  </head>
  <body>
    <?php
      if(isset($_SESSION["statut"]) && $_SESSION["statut"] == 2) {
        $options = [
          "default" => "Aucun service sélectionné",
          "meteo" => "Météo",
          "user" => "Utilisateur",
          "acoustic" => "Acoustique"
        ];

        echo "<section>";
        echo "<center>";
        echo "<p>";
        echo "<select>";

        foreach($options as $key => $value) {
          echo "<option value=\"" . $key . "\">" . $value . "</option>";
        }

        echo "</select>";
    ?>
            <div id="admin"></div>

            <a id="addRow" href="#">Ajouter une nouvelle ligne</a>
          </p>
        </center>
      </section>

      <script type="text/javascript" src="oXHR.js"></script>
      <script type="text/javascript">
        var table = null;

        document.querySelector("select").addEventListener("change", function(e){
          if(e.target.value != "default") {
            table = e.target.value;
            document.querySelector("#addRow").style.display = "inline-block";

            draw_db();
          }
        });

        document.querySelector("#addRow").addEventListener("click", function(e) {
          var inputs = document.querySelectorAll("input");

          insert(inputs);
          e.preventDefault();
        });

        function draw_db() {
          var request = new XMLHttpRequest();

          request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
              document.querySelector("#admin").innerHTML = request.responseText;

              var td = document.querySelectorAll(".data");
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
          var column = target.dataset.column;
          var value = target.textContent;
          var id = target.parentElement.firstChild.textContent;

          var request = new XMLHttpRequest();

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
          var columns = [];
          var values = [];

          inputs.forEach(function(els) {
            columns.push(els.dataset.column);
            values.push(els.value);
          });

          var request = new XMLHttpRequest();

          request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
              draw_db();
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
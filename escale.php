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
    <?php if(isset($_SESSION["statut"]) && $_SESSION["statut"] == 2) { ?>
      <section>
        <center>
          <p>
              <select>
                <option value="default">Aucun service sélectionné</option>
                <option value="meteo">Météo</option>
                <option value="user">Utilisateur</option>
                <option value="acoustic">Acoustique</option>
              </select>

            <div id="test"></div>
          </p>
        </center>
      </section>

      <script type="text/javascript" src="oXHR.js"></script>
      <script type="text/javascript">
        var select = document.querySelector("select");
        var table = null;

        select.addEventListener("change", function(e){
          table = e.target.value;

          if(table != "default") {
            draw_db();
          }
        });

        function draw_db() {
          var request = new XMLHttpRequest();

          request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
              document.querySelector("#test").innerHTML = request.responseText;

              var td = document.querySelectorAll(".data");
              td.forEach(function(els) {
                els.addEventListener("keydown", function(e) {
                  e.target.style.background = "#A61835";
                  e.target.style.color = "#1B1E24";
                  e.target.style.fontWeight = "bold";

                  if(e.keyCode == 13 || e.keyCode == 9) { // enter key or tab key
                    update(e.target);

                    e.target.style.background = "#EEEEEE";
                    e.target.style.color = "";
                    e.target.style.fontWeight = "normal";
                    
                    if(e.keyCode == 13) e.preventDefault();
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
          var column = target.dataset.table;
          var value = target.textContent;
          var id = target.parentElement.firstChild.textContent;

          var request = new XMLHttpRequest();

          request.open("POST", "administration.php");
          request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
          request.send("action=update&table=" + table + "&column=" + column + "&value=" + value + "&id=" + id);
        }
      </script>
    <?php } ?>
  </body>
</html>
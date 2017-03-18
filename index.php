<?php
  session_start();

  require_once("platforms/databases/UserDAO.php");
  require_once("nav.php");
  require_once("popup.php");

  if(isset($_SESSION["accesstoken"]) && isset($_SESSION["email"])) {
    $state = UserDAO::isConnected($_SESSION["accesstoken"], $_SESSION["email"]);
    $_SESSION["accesstoken"] = $state;
  } else {
    $state = false;
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Aérodrome Evreux Normandie</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="style/popup.css">
    <link rel="stylesheet" type="text/css" href="style/toggle.css">
    <link rel="stylesheet" type="text/css" href="style/home.css">
    <link rel="icon" type="image/png" href="res/logo.png">
  </head>
  <body>
    <header>
      <div class="container">
        <div class="centered">
          <div class="circle">
            <div class="logo"></div>
          </div>
          <div class="title">
            <h1>Aérodrome <span>Evreux Normandie</span></h1>
            <h2>Ecole de pilotage avion / ulm - Vol découverte et initiation.</h2>
          </div>
        </div>
      </div>
    </header>

    <section>
      <p>L'aéroclub d'Evreux est situé sur un terrain de la commune des Authieux aérodrome de Saint-André de l'Eure, code LFFD. L'entrée de l'aéroclub d'Evreux se trouve sur la commune des Authieux. Sortie n°11 de la N154 direction Saint-André.<br><br>
      Il comprend : un parking pour voitures. un grand hangar pour le stationnement des avions du club. un "Club-House", mis à la disposition de tous les membres et qui comprend, entre autres, un secrétariat, une salle de "briefing" et une salle de réunion. L'entretien et l'amélioration de ce local sont à la charge de tous. </p>
    </section>

    <?php if(!$state){ ?>
      <article>
        <div class="can-toggle demo-rebrand-1">
          <input id="d" type="checkbox">
          <label for="d">
            <div class="can-toggle__switch" data-checked="inscription" data-unchecked="connexion"></div>
          </label>
        </div>
      </article>

      <article id="inscription">
        <div class="bloc" class="formulaire">
          <div class="row">
            <input name="form" id="name" type="text" placeholder="Indiana Jones">
            <label for="name">Nom</label>
          </div>

          <div class="row">
            <input name="form" id="age" type="text" placeholder="37">
            <label for="age">Age</label>
          </div>

          <div class="row">
            <input name="form" id="email" type="email" placeholder="indiana.jones@aventure.com">
            <label for="email">Email</label>
          </div>

          <form class="code_input">
            <?php
              for($input = 0; $input < 6; $input++) {
                echo "<input type=\"password\" class=\"" . (2 << $input) . "\">";
              }
            ?>
          </form>
        </div>
      </article>

      <article id="connexion">
        <div class="bloc" class="formulaire">
          <div class="row">
            <input name="form" id="email" type="email" placeholder="indiana.jones@aventure.com">
            <label style="background: #61B2A7;" for="email">Email</label>
          </div>

          <form class="code_input">
            <?php
              for($input = 0; $input < 6; $input++) {
                echo "<input type=\"password\" class=\"" . (2 << $input) . "\">";
              }
            ?>
          </form>
        </div>
      </article>
    <?php } ?>

    <script type="text/javascript" src="libs/moment.js"></script>
    <script type="text/javascript" src="libs/moment-ferie-fr.js"></script>

    <?php if(!$state){ ?>
      <script type="text/javascript" src="controllers/oXHR.js"></script>
      <script type="text/javascript" src="app.popup.js"></script>
      <script type="text/javascript" src="controllers/AutotabMagic.js"></script>
      <script type="text/javascript">
        /* Jours fériés
        var zday = 1483279800;
        for(var day of moment().getFerieList(2017)){
          var input = day.date._d.toString();
          input = (moment(input).unix());

          if(zday >= input && zday < input + 24 * 3600){
            console.log("on", zday, input);
          } else {
            console.log("off", zday, input);
          }
        } */

        var count = 0;
        document.querySelector("#d").addEventListener("click", function(e){
          if(++count % 2 == 0) {
            document.querySelector("#inscription").style.display = "block";
            document.querySelector("#connexion").style.display = "none";
          } else {
            document.querySelector("#inscription").style.display = "none";
            document.querySelector("#connexion").style.display = "block";
          }
        });


        var list_of_errors = [
          "<span>L'email n'est pas valide</span>",
          "<span>L'email existe déjà</span>",
          "<span>Le nom doit contenir plus de 2 caractères</span>",
          "<span>L'âge est mal formaté</span>",
          "<span>Couple email/mot de passe inconnu</span>"
        ];

        var register = new Autotab();
        register.listen(document.querySelector("#inscription .code_input"), 1, function(keys, els) {
          let request = new XMLHttpRequest();
          request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
              if(request.responseText != "true" && request.responseText != "false"){
                var errors = request.responseText.split(":");
                var ul = document.createElement("ul");

                ul.innerHTML = "Les erreurs suivantes doivent être corrigées pour pouvoir continuer l'inscription :";

                for(var error of errors) {
                  if(error != 0) {
                    var li = document.createElement("li");
                    li.innerHTML = list_of_errors[error - 1];
                    ul.appendChild(li);

                    popup.manager.open(ul);
                    register.clear(els);
                  }
                }
              }
            } else if(request.responseText == "true") {
              location.href = "index.php";
            }
          }

          request.open("POST", "subscribe.php");
          request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
          request.send("name=" + document.querySelector("#inscription #name").value +
                       "&age=" + document.querySelector("#inscription #age").value +
                       "&email=" + document.querySelector("#inscription #email").value +
                       "&password=" + keys);
        });

        var login = new Autotab();
        login.listen(document.querySelector("#connexion .code_input"), 1, function(keys, els) {
          let request = new XMLHttpRequest();
          request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
              if(request.responseText != "true" && request.responseText != "false"){
                var errors = request.responseText.split(":");
                var ul = document.createElement("ul");

                ul.innerHTML = "Les erreurs suivantes doivent être corrigées pour pouvoir vous connectez :";

                for(var error of errors) {
                  if(error != 0) {
                    var li = document.createElement("li");
                    li.innerHTML = list_of_errors[error - 1];
                    ul.appendChild(li);

                    popup.manager.open(ul);
                    login.clear(els);
                  }
                }
              }
            } else if(request.responseText == "true") {
              location.href = "index.php";
            }
          }

          request.open("POST", "login.php");
          request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
          request.send("email=" + document.querySelector("#connexion #email").value +
                       "&password=" + keys);
        });
      </script>
    <?php } ?>
  </body>
</html>
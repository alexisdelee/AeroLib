<?php
  require_once("init.php");
  require_once("nav.php");
  require_once("popup.php");
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Aérodrome Evreux Normandie</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="style/popup.css">
    <link rel="stylesheet" type="text/css" href="style/toggle.css">
    <link rel="stylesheet" type="text/css" href="style/home.css">
    <link rel="stylesheet" type="text/css" href="style/component.css">
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

    <?php if(!$router->state){ ?>
      <section>
        <p>L'aéroclub d'Evreux est situé sur un terrain de la commune des Authieux aérodrome de Saint-André de l'Eure, code LFFD. L'entrée de l'aéroclub d'Evreux se trouve sur la commune des Authieux. Sortie n°11 de la N154 direction Saint-André.<br><br>
        Il comprend : un parking pour voitures. un grand hangar pour le stationnement des avions du club. un "Club-House", mis à la disposition de tous les membres et qui comprend, entre autres, un secrétariat, une salle de "briefing" et une salle de réunion. L'entretien et l'amélioration de ce local sont à la charge de tous. </p>
      </section>

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
            <input name="form" id="date" type="text" placeholder="01/07/1899">
            <label for="date">Date</label>
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
    <?php } else { ?>
      <section style="padding: 100px 0;">
        <div id="menu" class="nav">
          <ul>
            <li>
              <a href="escale.php">
                <span class="icon">
                  <i aria-hidden="true" class="icon-home"></i>
                </span>
                <span>Escale</span>
              </a>
            </li>
            <li>
              <a href="aeroclub.php">
                <span class="icon"> 
                  <i aria-hidden="true" class="icon-services"></i>
                </span>
                <span>
                  Aéroclub
                </span>
              </a>
            </li>
            <li>
              <a href="account.php">
                <span class="icon">
                  <i aria-hidden="true" class="icon-portfolio"></i>
                </span>
                <span>Compte</span>
              </a>
            </li>
            <li>
              <a href="localisation.php">
                <span class="icon">
                  <i aria-hidden="true" class="icon-prevision"></i>
                </span>
                <span>Prévision</span>
              </a>
            </li>
          </ul>
        </div>

        <center style="margin-top: 160px;">Vous pouvez aussi télécharger le planning des réservations de l'aéroclub <a href="bin/aen_planning_setup.exe">ici</a> <small>(disponible uniquement au sein du complexe)</small>.</center>
      </section>
    <?php } ?>

    <?php if(!$router->state){ ?>
      <script type="text/javascript" src="controllers/oXHR.js"></script>
      <script type="text/javascript" src="controllers/Request.js"></script>
      <script type="text/javascript" src="controllers/AutotabMagic.js"></script>
      <script type="text/javascript" src="app.popup.js"></script>
      <script type="text/javascript">
        let count = 0;
        document.querySelector("#d").addEventListener("click", (e) => {
          if(++count % 2 == 0) {
            document.querySelector("#inscription").style.display = "block";
            document.querySelector("#connexion").style.display = "none";
          } else {
            document.querySelector("#inscription").style.display = "none";
            document.querySelector("#connexion").style.display = "block";
          }
        });


        let list_of_errors = [
          "<span>L'email n'est pas valide</span>",
          "<span>L'email existe déjà</span>",
          "<span>Le nom doit contenir plus de 2 caractères</span>",
          "<span>La date de naissance est mal formatée</span>",
          "<span>Couple email/mot de passe inconnu</span>"
        ];

        let register = new Autotab();
        register.listen(document.querySelector("#inscription .code_input"), 1, (keys, els) => {
          let request = new Request();
          request.post("subscribe.php",
            "name=" + document.querySelector("#inscription #name").value +
            "&date=" + document.querySelector("#inscription #date").value +
            "&email=" + document.querySelector("#inscription #email").value +
            "&password=" + keys, (response) => {
              console.log(response);
              if(response !== "true") {
                let errors = response.split(":");

                let box = "<ul>Les erreurs suivantes doivent être corrigées pour pouvoir continuer l'inscription :";
                for(let error of errors) {
                  if(error != 0) {
                    box += "<li>" + list_of_errors[error - 1] + "</li>";
                  }
                }

                box += "</ul>";
                popup.manager.open(box);
                register.clear(els);
              } else {
                location.href = "index.php";
              }
            });
        });

        let login = new Autotab();
        login.listen(document.querySelector("#connexion .code_input"), 1, (keys, els) => {
          let request = new Request();

          request.post("login.php",
            "email=" + document.querySelector("#connexion #email").value +
            "&password=" + keys, (response) => {
              if(response !== "true") {
                let errors = response.split(":");

                let box = "<ul>Les erreurs suivantes doivent être corrigées pour pouvoir vous connecter :";
                for(let error of errors) {
                  if(error != 0) {
                    box += "<li>" + list_of_errors[error - 1] + "</li>";
                  }
                }

                box += "</ul>";
                popup.manager.open(box);
                login.clear(els);
              } else {
                location.href = "index.php";
              }
            });
        });
      </script>
    <?php } ?>
  </body>
</html>
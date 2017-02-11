<!DOCTYPE html>
<html>
  <head>
    <title>Aérodrome Evreux Normandie</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="style/home.css">
    <link rel="icon" type="image/png" href="res/logo.png">
  </head>
  <body>
    <?php require "nav.php"; ?>

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

    <article>
      <div class="bloc">
        <!-- <h1>S'inscrire ne prend que quelques secondes...</h1> -->

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
      </div>
    </article>
  </body>
</html>
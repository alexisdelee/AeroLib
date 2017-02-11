<?php $connect = true; ?>

<nav>
  <div class="centered">
    <div class="nav-services">
      <a class="nav-service active" href="index.php">Accueil</a>
      <a class="nav-service" href=".">Escale</a>
      <a class="nav-service" href=".">Aéroclub</a>
    </div>

    <?php if($connect){ ?>
      <div class="nav-pages">
        <a href=".">Compte</a>
        <a href="localisation.php">Prévision</a>
        <a href=".">Déconnexion</a>
      </div>
    <?php } else { ?>
      <div class="nav-pages">
        <a href="localisation.php">Localisation</a>
        <a href=".">Connexion</a>
      </div>
    <?php } ?>
  </div>
</nav>
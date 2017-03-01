<nav>
  <div class="centered">
    <div class="nav-services">
      <a class="nav-service active" href="index.php">Accueil</a>
      <a class="nav-service" href=".">Escale</a>
      <a class="nav-service" href=".">Aéroclub</a>
    </div>

    <?php if(isset($_SESSION["accesstoken"])){ ?>
      <div class="nav-pages">
        <a href="account.php">Compte</a>
        <a href="localisation.php">Prévision</a>
        <a href="logout.php">Déconnexion</a>
      </div>
    <?php } ?>
  </div>
</nav>
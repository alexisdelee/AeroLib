<?php
  require_once("init.php");
?>

<nav>
  <div class="centered">
    <?php if(!$router->state) { ?>
      <div class="nav-services">
        <a class="nav-service active" href="index.php">Accueil</a>
        <a class="nav-service" href="escale.php">Escale</a>
        <a class="nav-service" href="aeroclub.php">Aéroclub</a>
      </div>
    <?php } else if($router->permission == 3) { ?>
      <div class="nav-services">
        <a class="nav-service active" href="index.php">Accueil</a>
      </div>

      <div class="nav-pages">
        <a href="account.php">Compte</a>
        <a href="logout.php">Déconnexion</a>
      </div>
    <?php } else { ?>
      <div class="centered">
        <div class="nav-services">
          <a class="nav-service active" href="index.php">Accueil</a>
          <a class="nav-service" href="escale.php">Escale</a>
          <a class="nav-service" href="aeroclub.php">Aéroclub</a>
        </div>
        
        <?php
          $manager = PDOUtils::getSharedInstance();
          $services = 0;

          $lastIdService = $manager->getAll("
            SELECT receipt.idReceipt
            FROM `receipt`
              LEFT JOIN `user` ON receipt.idUser = user.idUser
            WHERE user.email = ?
            ORDER BY receipt.idReceipt DESC
            LIMIT 1
          ", [$_SESSION["email"]]);

          if(!empty($lastIdService)) {
            $services = $manager->getAll("
              SELECT count(idService) AS total
              FROM `service`
              WHERE idReceipt = ?
              LIMIT 1
            ", [$lastIdService[0]["idReceipt"]]);

            if(isset($services)) {
              $services = $services[0]["total"];
            } else {
              $services = 0;
            }
          }
        ?>

        <div class="nav-pages">
          <a href="basket.php">Panier <?php echo "<small style=\"color: #A61835;\"><strong>[" . $services . "]</strong></small>"; ?></a>
          <a href="account.php">Compte</a>
          <a href="localisation.php">Prévision</a>
          <a href="logout.php">Déconnexion</a>
        </div>
      </div>
    <?php } ?>
</nav>
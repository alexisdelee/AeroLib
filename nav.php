<?php
  require_once("init.php");
?>

<nav>
  <div class="centered">
    <div class="nav-services">
      <a class="nav-service active" href="index.php">Accueil</a>
      <a class="nav-service" href="escale.php">Escale</a>
      <a class="nav-service" href=".">Aéroclub</a>
    </div>

    <?php if($router->state){ ?>
      <?php
        $manager = PDOUtils::getSharedInstance();
        $services = $manager->getAll("
          SELECT count(service.idService)
          FROM `service`
            LEFT JOIN `receipt` ON service.idReceipt = receipt.idReceipt
          WHERE receipt.idUser =
            (SELECT idUser
            FROM `user`
            WHERE email = ?)
            AND isPaid = 0
          ORDER BY receipt.idReceipt
          LIMIT 1
        ", [$_SESSION["email"]]);

        if(isset($services)) {
          $services = $services[0]["count(service.idService)"];
        } else {
          $services = 0;
        }
      ?>

      <div class="nav-pages">
        <a href="basket.php">Panier <?php echo "<small style=\"color: #A61835;\"><strong>[" . $services . "]</strong></small>"; ?></a>
        <a href="account.php">Compte</a>
        <a href="localisation.php">Prévision</a>
        <a href="logout.php">Déconnexion</a>
      </div>
    <?php } ?>
  </div>
</nav>
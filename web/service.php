<?php
  require_once("init.php");

  setLocale(LC_ALL, "fr_FR");

  if(isset($_POST["type"])) {
    if($_POST["type"] === "remove" && isset($_POST["service"])) {
      $manager = PDOUtils::getSharedInstance();

      $manager->exec("
        DELETE FROM `service`
        WHERE idService = ?
      ", [$_POST["service"]]);

      exit(666);
    } else if($_POST["type"] === "add") {
      $manager = PDOUtils::getSharedInstance();

      $info = $manager->getAll("
        SELECT @idReceipt := receipt.idReceipt AS idReceipt, @cost := SUM(service.costService) AS cost, @tva := SUM(service.tvaService) AS tva
        FROM `service`
        LEFT JOIN `receipt` ON receipt.idReceipt = service.idReceipt
        WHERE service.idReceipt =
          (SELECT receipt.idReceipt
          FROM `receipt`
            LEFT JOIN `user` ON receipt.idUser = user.idUser
          WHERE user.email = ?
            AND user.credit >= 0 # uniquement si le crédit est positif
          ORDER BY receipt.idReceipt DESC
          LIMIT 1);
      ", [$_SESSION["email"]]);

      if($info[0]["idReceipt"] === null) {
        echo "Vous n'avez pas assez de crédit pour valider la transaction.";
        exit(666);
      }

      $user = $manager->getAll("
        SELECT user.idUser, user.credit
        FROM `user`
          LEFT JOIN `receipt` ON receipt.idUser = user.idUser
        WHERE receipt.idReceipt = ?;
      ", [$info[0]["idReceipt"]]);

      if(empty($user[0]["idUser"])) {
        echo "Erreur interne.";
        exit(666);
      }

      if($user[0]["credit"] >= $info[0]["cost"] + $info[0]["tva"]) {
        $manager->exec("
          UPDATE `receipt`
          SET totalCost = ?,
              totalTva = ?,
              isPaid = 1,
              creation = ?
          WHERE idReceipt = ?;

          UPDATE `user`
          SET credit = credit - ?
          WHERE idUser = ?;

          INSERT `receipt`(idUser) VALUES(?);
        ", [$info[0]["cost"], $info[0]["tva"], time(), $info[0]["idReceipt"], $info[0]["cost"] + $info[0]["tva"], $user[0]["idUser"], $user[0]["idUser"]]);
      } else {
        $manager->exec("
        UPDATE `receipt`
        SET totalCost = ?,
            totalTva = ?,
            isPaid = 0,
            creation = ?
        WHERE idReceipt = ?;

        INSERT `receipt`(idUser) VALUES(?);
      ", [$info[0]["cost"], $info[0]["tva"], time(), $info[0]["idReceipt"], $user[0]["idUser"]]);
      }

      echo "ok";
      exit(666);
    } else if($_POST["type"] === "paid" && isset($_POST["receipt"])) {
      $manager = PDOUtils::getSharedInstance();

      $user = $manager->getAll("
        SELECT user.idUser, receipt.totalCost, receipt.totalTva
        FROM `receipt`
          LEFT JOIN `user` ON receipt.idUser = user.idUser
        WHERE user.email = ?
          AND receipt.idReceipt = ?
          AND user.credit >= 
            (SELECT totalCost + totalTva
            FROM `receipt`
            WHERE idReceipt = ?);
      ", [$_SESSION["email"], $_POST["receipt"], $_POST["receipt"]]);

      if(!empty($user)) {
        $manager->exec("
          UPDATE `user`
          SET credit = credit - ?
          WHERE idUser = ?;

          UPDATE `receipt`
          SET isPaid = 1
          WHERE idReceipt = ?;
        ", [$user[0]["totalCost"] + $user[0]["totalTva"], $user[0]["idUser"], $_POST["receipt"]]);

        echo "ok";
        exit(666);
      }

      echo "Veuillez transférer plus d'argent pour continuer le paiement.";
      exit(666);
    }
  }
?>
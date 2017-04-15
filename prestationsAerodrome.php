<?php
  require_once("init.php");

  setLocale(LC_ALL, "fr_FR");

  if(isset($_POST["data"])) {
    $data = json_decode($_POST["data"], true);

    $manager = PDOUtils::getSharedInstance();
    $plane = $manager->getAll("
      SELECT idPlane, surface, mass, base
      FROM `plane`
      WHERE matricule = ?
    ", [$data["matricule"]]);

    if(empty($plane)) {
      echo "Ce matricule n'est pas enregistré.";
      exit(666);
    }

    $date = new DateTime();
    $date->setTimestamp($data["date"]);

    if($data["prestation"] === "avitaillement") {
      if($date < (new DateTime(date_default_timezone_get()))->add(new DateInterval("PT2H"))) { // inférieur à 2 heures à partir de la date actuelle
        echo "Il faut réserver minimum 2h avant la prestation.";
        exit(666);
      } else if($date > (new DateTime(date_default_timezone_get()))->add(new DateInterval("P1Y"))) { // supérieur à 1 an à partir de la date actuelle
        echo "L'interval de réservation est limité à un an.";
        exit(666);
      }

      $result = $manager->getAll("
        SELECT costReservoir, tvaReservoir
        FROM `reservoir`
        WHERE product = ?
      ", [$data["options"]["product"]]);

      if(empty($result)) {
        echo "Ce produit n'est pas référencé dans la base de données de l'aérodrome.";
        exit(666);
      }

      $data["options"]["quantite"] = intval($data["options"]["quantite"]);
      if($data["options"]["quantite"] > 0 && $data["options"]["quantite"] <= 1500) {
        $cost = $data["options"]["quantite"] * $result[0]["costReservoir"];
        $tva = $data["options"]["quantite"] * $result[0]["tvaReservoir"];

        $manager->exec("
          INSERT INTO `service`(description, subscription, inscription, dateStart, dateEnd, idReceipt, idPlane, costService, tvaService)
          VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [$data["options"]["quantite"] . "L de " . utf8_decode($data["options"]["product"]), time(), $data["date"], $data["date"], $data["date"], lastReceipt(), $plane[0]["idPlane"], $cost, $tva]);
      
        $_SESSION["prestation"] = [
          "cost" => $cost,
          "tva" => $tva
        ];
        echo "ok";
        exit(666);
      }
    } else if($data["prestation"] === "nettoyage") {
      if($date < (new DateTime(date_default_timezone_get()))->add(new DateInterval("PT2H"))) { // inférieur à 2 heures à partir de la date actuelle
        echo "Il faut réserver minimum 2h avant la prestation.";
        exit(666);
      } else if($date > (new DateTime(date_default_timezone_get()))->add(new DateInterval("P1Y"))) { // supérieur à 1 an à partir de la date actuelle
        echo "L'interval de réservation est limité à un an.";
        exit(666);
      }

      $result = $manager->getAll("
        SELECT costCleaning, tvaCleaning
        FROM `cleaning`
      ");

      if(empty($result)) {
        echo "Erreur interne.";
        exit(666);
      }

      $cost = $plane[0]["surface"] * $result[0]["costCleaning"];
      $tva = $plane[0]["surface"] * $result[0]["tvaCleaning"];

      $manager->exec("
        INSERT INTO `service`(description, subscription, inscription, dateStart, dateEnd, idReceipt, idPlane, costService, tvaService)
        VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)
      ", ["nettoyage", time(), $data["date"], $data["date"], $data["date"], lastReceipt(), $plane[0]["idPlane"], $cost, $tva]);
    
      $_SESSION["prestation"] = [
        "cost" => $cost,
        "tva" => $tva
      ];
      echo "ok";
      exit(666);
    } else if($data["prestation"] === "stationnement") {
      if($date < (new DateTime(date_default_timezone_get()))->add(new DateInterval("PT2H"))) { // inférieur à 2 heures à partir de la date actuelle
        echo "Il faut réserver minimum 2h avant la prestation.";
        exit(666);
      } else if($date > (new DateTime(date_default_timezone_get()))->add(new DateInterval("P1Y"))) { // supérieur à 1 an à partir de la date actuelle
        echo "L'interval de réservation est limité à un an.";
        exit(666);
      }

      if($data["options"]["zone"] === "exterieur") {
        $parking = $manager->getAll("
          SELECT costOutdoorParking, tvaOutdoorParking
          FROM `outdoorparking`
        ");

        if(empty($parking)) {
          echo "Erreur interne.";
          exit(666);
        }

        if($data["options"]["end"] - $data["date"] <= 0) {
          echo "Les dates de début et fin de prestation ne conviennent pas.";
          exit(666);
        }

        $week = ceil(($data["options"]["end"] - $data["date"]) / (7 * 24 * 3600));

        $cost = $plane[0]["surface"] * $week * $parking[0]["costOutdoorParking"];
        $tva = $plane[0]["surface"] * $week * $parking[0]["tvaOutdoorParking"];

        $manager->exec("
          INSERT INTO `service`(description, subscription, inscription, dateStart, dateEnd, idReceipt, idPlane, costService, tvaService)
          VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [utf8_decode("stationnement extérieur de " . $week . " semaine(s)"), time(), $data["date"], $data["date"], $data["options"]["end"], lastReceipt(), $plane[0]["idPlane"], $cost, $tva]);
      
        $_SESSION["prestation"] = [
          "cost" => $cost,
          "tva" => $tva
        ];
        echo "ok";
        exit(666);
      } else if($data["options"]["zone"] === "interieur") {
        if($data["options"]["end"] - $data["date"] <= 0) {
          echo "Les dates de début et fin de prestation ne conviennent pas.";
          exit(666);
        }

        $days = ceil(($data["options"]["end"] - $data["date"]) / (24 * 3600)); // nombre de jours entamés

        $category = $manager->getAll("
          SELECT category.costCategory, category.tvaCategory, category.ratio
          FROM `indoorparking`
            LEFT JOIN `category` ON indoorparking.idCategory = category.typeCategory
          WHERE indoorparking.minMass < ?
            AND indoorparking.maxMass > ?
            AND indoorparking.minSurface < ?
            AND indoorparking.maxSurface > ?
            AND category.timetable = ?
            AND category.base = ?
          LIMIT 1
        ", [$plane[0]["mass"] / 1000, $plane[0]["mass"] / 1000, $plane[0]["surface"], $plane[0]["surface"], utf8_decode($data["options"]["tarif"]), $plane[0]["base"]]);

        if(empty($category)) {
          echo "Votre demande n'a pas pu aboutir. Veuillez réessayer.";
          exit(666);
        }

        $cost = floatval($category[0]["costCategory"]) * ceil($days / floatval($category[0]["ratio"]));
        $tva = floatval($category[0]["tvaCategory"]) * ceil($days / floatval($category[0]["ratio"]));

        $manager->exec("
          INSERT INTO `service`(description, subscription, inscription, dateStart, dateEnd, idReceipt, idPlane, costService, tvaService)
          VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [utf8_decode("stationnement intérieur de " . $days . " jours"), time(), $data["date"], $data["date"], $data["options"]["end"], lastReceipt(), $plane[0]["idPlane"], $cost, $tva]);
      
        $_SESSION["prestation"] = [
          "cost" => $cost,
          "tva" => $tva
        ];
        echo "ok";
        exit(666);
      } else {
        echo "Aucune zone de stationnement n'a été sélectionnée.";
      }
    } else {
      echo "Cette prestation n'est pas présente dans cet aérodrome.";
    }
  }

  function lastReceipt() {
    $manager = PDOUtils::getSharedInstance();
    $_id = $manager->getAll("
      SELECT idReceipt
      FROM `receipt`
        LEFT JOIN `user` ON receipt.idUser = user.idUser
      WHERE user.email = ?
      ORDER BY idReceipt DESC
    ", [$_SESSION["email"]]);

    if(empty($_id)) return null;
    else return $_id[0]["idReceipt"];
  }
?>
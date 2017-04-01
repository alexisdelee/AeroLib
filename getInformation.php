<?php
  require_once("init.php");

  if(isset($_POST["timetable"], $_POST["model"])) {
    $manager = PDOUtils::getSharedInstance();
    $result = $manager->getAll("SELECT timetable, costLanding, tvaLanding, package FROM `landing` LEFT JOIN `model` ON landing.idModel = model.idModel WHERE model.typeModel = ?", [utf8_decode($_POST["model"])]);

    if(empty($result)) {
      echo "undefined";
    } else {
      foreach($result as $data) {
        if($data["package"] === "0") {
          if($data["timetable"] === $_POST["timetable"]) {
            echo "1:" . utf8_encode(implode(":", $data));
          } else {
            echo "0:" . utf8_encode(implode(":", $data));
          }
        } else {
          echo "1:" . utf8_encode(implode(":", $data));
        }

        echo "#";
      }
    }
  } else if(isset($_POST["get"])) {
    if($_POST["get"] === "forfait" && isset($_POST["date"])) {
      $manager = PDOUtils::getSharedInstance();
      $isUsed = $manager->getAll("
        SELECT service.idService FROM `basket` LEFT JOIN `service` ON basket.idService = service.idService
        WHERE service.prestation = \"Atterrissage\"
          AND service.usage - 5 * 60 <= ?
          AND service.usage + 5 * 60 >= ?
          AND
            (SELECT receipt.idReceipt FROM `receipt` LEFT JOIN `user` ON receipt.idUser = user.idUser
            WHERE user.email <> ?)
      ", [$_POST["date"], $_POST["date"], $_SESSION["email"]]);

      if(empty($isUsed)) {
        $isAvailable = $manager->getAll("
          SELECT idReceipt FROM `basket` LEFT JOIN `service` ON basket.idService = service.idService
          WHERE service.dateStart <= ?
            AND service.dateEnd >= ?
            AND service.remainingLicences <> 0
            AND
              (SELECT receipt.idReceipt FROM `receipt` LEFT JOIN `user` ON receipt.idUser = user.idUser
              WHERE user.email = ?)
        ", [$_POST["date"], $_POST["date"], $_SESSION["email"]]);

        if(empty($isAvailable)) {
          echo "no-forfait";
        } else {
          echo "forfait";
        }
      } else {
        echo "unavailable";
      }
    } else if($_POST["get"] === "model") {
      $manager = PDOUtils::getSharedInstance();
      $result = $manager->getAll("SELECT typeModel FROM `model`");

      if(empty($result)) {
        echo "undefined";
      } else {
        echo utf8_encode(implode(":", array_map(function($value) {
          return $value["typeModel"];
        }, $result)));
      }
    }
  } else if(isset($_POST["type"], $_POST["data"])) {
    $data = json_decode($_POST["data"], true); // demande à ce qu'il soit converti en tableau et non en objet (stdClass)
    $cost = 0;
    $tva = 0;

    if($_POST["type"] === "landing" && isset($_POST["domain"])) {
      if($_POST["domain"] === "register") {
        $manager = PDOUtils::getSharedInstance();
        $model = $manager->getAll("SELECT idModel FROM `model` WHERE typeModel = ?", [utf8_decode($data["model"])]);

        if(!empty($model)) {
          $landing = $manager->getAll("SELECT costLanding, tvaLanding FROM `landing` WHERE idModel = ? AND timetable = ?", [$model[0]["idModel"], utf8_decode($data["service"])]);
          
          if(!empty($landing)) {
            $cost += floatval($landing[0]["costLanding"]);
            $tva += floatval($landing[0]["tvaLanding"]);

            if($data["service"] === "Unité") {
              $cost *= $data["duration"];
              $tva *= $data["duration"];
            }
            
            $acoustic = $manager->getAll("SELECT idAcoustic, " . ($data["state"] === "day" ? "coefficientDay" : "coefficientNight") . " FROM `acoustic` WHERE groupAcoustic = ?", [$data["acoustic"]]);

            if(!empty($acoustic)) {
              $remittance = $manager->getAll("SELECT percent FROM `remittance` WHERE idAcoustic = ?", [$acoustic[0]["idAcoustic"]]);

              if(!empty($remittance)) {
                $cost = $cost - $cost * floatval($remittance[0]["percent"]) / 100;
                $tva = $tva - $tva * floatval($remittance[0]["percent"] / 100);
              }

              $cost *= $acoustic[0][($data["state"] === "day" ? "coefficientDay" : "coefficientNight")];
              $tva *= $acoustic[0][($data["state"] === "day" ? "coefficientDay" : "coefficientNight")];

              if($data["service"] === "Mensuel") {
                $date = new DateTime();
                $date->setTimestamp($data["prestation"]);
                $date->add(new DateInterval("P1M")); // on ajoute un mois
                $idService = $manager->exec("INSERT INTO `service`(prestation, dateStart, dateEnd, usage, remainingLicences) VALUES(\"Atterrissage\", ?, ?, ?, ~0 >> 33)", [$data["prestation"], $date->getTimestamp(), $data["prestation"]], true);

                $description = "Abonnement mensuel";
              } else if($data["service"] === "Unité") {
                $idService = $manager->exec("INSERT INTO `service`(prestation, dateStart, dateEnd, usage, remainingLicences) VALUES(\"Atterrissage\", ?, ?, ?, ?)", [$data["prestation"], $data["prestation"], $data["duration"], $data["prestation"]], true);

                $description = "Abonnement à l'unité pour " . $data["duration"] . " jours";
              } else {
                $idService = $manager->exec("INSERT INTO `service`(prestation, dateStart, dateEnd, usage, remainingLicences) VALUES(\"Atterrissage\", ?, ?, 1)", [$data["prestation"], $data["prestation"], $data["prestation"]], true);

                $description = "Forfait " . strtolower($data["service"]);
              }

              // insertion d'un nouvel avion ou update de l'ancien
              $idReceipt = $manager->getAll("SELECT idReceipt FROM `receipt` LEFT JOIN `user` ON receipt.idUser = user.idUser WHERE user.email = ? ORDER BY idReceipt DESC LIMIT 1", [$_SESSION["email"]]);

              if(empty($idReceipt)) {
                echo "undefined";
                exit(666);
              }

              $plane = $manager->getAll("SELECT idPlane FROM `receipt` LEFT JOIN `basket` ON receipt.idReceipt = basket.idReceipt WHERE basket.idReceipt = ? ORDER BY basket.idBasket DESC LIMIT 1", [$idReceipt[0]["idReceipt"]]);

              if(!empty($plane)) { // mise à jour des données de l'avion
                $manager->exec("UPDATE `plane` 
                  SET idModel = ?, 
                  idAcoustic = ?
                WHERE idPlane = ?", [$model[0]["idModel"], $acoustic[0]["idAcoustic"], $plane[0]["idPlane"]]);

                $manager->exec("INSERT INTO `basket`(description, subscription, idPlane, idService, idReceipt, costService, tvaService) 
                  VALUES(?, ?, ?, ?, ?, ?, ?)", [utf8_decode($description), $data["date"], $plane[0]["idPlane"], $idService, $idReceipt[0]["idReceipt"], $cost, $tva]);
              } else { // insertion d'un nouvel avion
                $idPlane = $manager->exec("INSERT INTO `plane`(idModel, idAcoustic) VALUES(?, ?)", [$model[0]["idModel"], $acoustic[0]["idAcoustic"]], true);

                $manager->exec("INSERT INTO `basket`(description, subscription, idPlane, idService, idReceipt, costService, tvaService) 
                  VALUES(?, ?, ?, ?, ?, ?, ?)", [utf8_decode($description), $data["date"], $idPlane, $idService, $idReceipt[0]["idReceipt"], $cost, $tva]);
              }

              echo $cost . ":" . $tva;
              exit(666);
            }
          }
        }
      } else if($_POST["domain"] === "update") {
        $manager = PDOUtils::getSharedInstance();
        $result = $manager->getAll("
          SELECT service.idService FROM `basket` LEFT JOIN `service` ON basket.idService = service.idService
          WHERE service.usage <> service.lastUsage
          AND
            (SELECT receipt.idReceipt FROM `receipt` LEFT JOIN `user` ON receipt.idUser = user.idUser
            WHERE user.email = ?) = basket.idReceipt;
        ", [$_SESSION["email"]]);

        exit(666);
      }
    }

    echo "undefined";
  }
?>
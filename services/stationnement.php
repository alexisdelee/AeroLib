<?php
  require_once("../platforms/databases/PDOUtils.php");

  setLocale(LC_ALL, "fr_FR");
  header("Content-Type: application/json; charset=utf-8");

  $res = [
    "message" => "",
    "description" => "stationnement",
    "subscription" => time(),
    "inscription" => 0,
    "options" => [
      "id_plane" => -1,
      "cost" => 0,
      "tva" => 0,
      "start" => 0,
      "end" => 0
    ]
  ];

  if(isset($_POST["action"], $_POST["duration"], $_POST["matricule"], $_POST["timetable_area"], $_POST["zone"], $_POST["email"])) {
    if(is_nan($_POST["action"]) || is_nan($_POST["duration"]) || strlen($_POST["matricule"]) != 8
      || $_POST["action"] < 0) {
      _response_code(409, "Valeurs saisies incorrectes", $res);
    }

    $fullday_action = wholeDay($_POST["action"]);
    $fullday_now = wholeDay($res["subscription"]);

    $_POST["duration"] = abs($_POST["duration"]); // on évite les valeurs négatives ainsi

    if($fullday_action - $fullday_now < 48 * 3600) { // intervale de deux jours minimum
      _response_code(400, "Il faut réserver minimum 48h avant la prestation", $res);
    } else if($fullday_action + $_POST["duration"] - $fullday_now > 365 * 24 * 3600) { // intervale d'un an maximum
      _response_code(400, "Impossible de réserver plus d'un an avant la prestation", $res);
    }

    $fullday_end = $fullday_action + $_POST["duration"] * 24 * 3600; // récupération de la journée de fin de prestation

    // récupération du prix hors taxes et de la tva à partir de $_POST["timetable_area"]
    $manager = PDOUtils::getSharedInstance();

    $plane = $manager->getAll("
      SELECT plane.idPlane, plane.base, plane.surface, plane.mass FROM `plane`
        LEFT JOIN `user` ON plane.idUser = user.idUser
      WHERE user.email = ?
        AND plane.matricule = ?
    ", [$_POST["email"], $_POST["matricule"]]); // on récupère des informations sur l'avion sélectionné

    if(empty($plane)) {
      _response_code(404, "Impossible de trouver les données en rapport avec votre recherche (erreur liée à votre matricule)", $res);
    }

    switch($_POST["zone"]) {
      case "exterieur":
        $remainingPlace = $manager->getAll("
          SELECT COUNT(idService) AS place FROM `service`
          WHERE description REGEXP '^stationnement extérieur.*$'
            AND dateStart <= ?
            AND dateEnd >= ?
        ", [$fullday_action, $fullday_end]); // on récupère le nombre d'avions stockés à l'intérieur de l'intervale [$fullday_action ; $fullday_end]

        if(!isset($remainingPlace) || floatval($remainingPlace[0]["place"]) >= 20) { // le parking extérieur de l'aérodrome ne peut contenir que 20 avions
          _response_code(401, "Aucune place restante n'est disponible dans le hangar", $res);
        }

        $prestation = $manager->getAll("
          SELECT costOutdoorParking, tvaOutdoorParking FROM `outdoorparking`
          ORDER BY idOutdoorParking DESC
          LIMIT 1
        ");

        if(empty($prestation)) {
          _response_code(404, "Impossible de trouver les données en rapport avec votre recherche (erreur liée à la redevance pour stationnement extérieur)", $res);
        }

        $weeks = ceil(($fullday_end - $fullday_action) / (7 * 24 * 3600)); // calcul du nombre de semaines indivisibles

        $res["description"] .= " extérieur de " . $weeks . ($weeks > 1 ? " semaines" : " semaine");
        $res["options"]["cost"] = floatval($plane[0]["surface"]) * $weeks * floatval($prestation[0]["costOutdoorParking"]);
        $res["options"]["tva"] = floatval($plane[0]["surface"]) * $weeks * floatval($prestation[0]["tvaOutdoorParking"]);
        
        break;
      case "interieur":
        $remainingPlace = $manager->getAll("
          SELECT COUNT(idService) AS place FROM `service`
          WHERE description REGEXP '^stationnement intérieur.*$'
            AND dateStart <= ?
            AND dateEnd >= ?
        ", [$fullday_action, $fullday_end]); // on récupère le nombre d'avions stockés à l'intérieur de l'intervale [$fullday_action ; $fullday_end]

        if(empty($remainingPlace) || floatval($remainingPlace[0]["place"]) >= 6) { // le hangar ne peut contenir que 6 avions
          _response_code(401, "Aucune place restante n'est disponible dans le hangar", $res);
        }

        $days = ceil(($fullday_end - $fullday_action) / (24 * 3600)); // nombre de jours entamés

        $prestation = $manager->getAll("
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
        ", [$plane[0]["mass"] / 1000, $plane[0]["mass"] / 1000, $plane[0]["surface"], $plane[0]["surface"], utf8_decode($_POST["timetable_area"]), $plane[0]["base"]]);

        if(empty($prestation)) {
          _response_code(404, "Impossible de trouver les données en rapport avec votre recherche (erreur liée à la redevance d'abris)", $res);
        }

        $res["description"] .= " intérieur de " . $_POST["duration"] . ($_POST["duration"] > 1 ? " jours" : " jour");
        $res["options"]["cost"] = floatval($prestation[0]["costCategory"]) * ceil($days / floatval($prestation[0]["ratio"]));
        $res["options"]["tva"] = floatval($prestation[0]["tvaCategory"]) * ceil($days / floatval($prestation[0]["tvaCategory"]));

        break;
      default:
        _response_code(404, "Impossible de trouver les données en rapport avec votre recherche (erreur liée au lieu de stationnement)", $res);
    }

    $res["inscription"] = (int)$_POST["action"];
    $res["options"]["id_plane"] = (int)$plane[0]["idPlane"];
    $res["options"]["start"] = $fullday_action;
    $res["options"]["end"] = $fullday_end + 23 * 3600 + 59 * 60 + 59; // fin de la jourée (23h59'59)
    
    _response_code(200, "ok", $res);
  } else {
    _response_code(401, "Autorisation refusée", $res);
  }

  function wholeDay($timestamp) {
    $info = getdate($timestamp);
    return mktime(0, 0, 0, $info["mon"], $info["mday"], $info["year"]);
  }

  function _response_code($error, $message, $ressource) {
    http_response_code($error);

    $ressource["message"] = $message;

    echo json_encode($ressource);
    exit($error);
  }
?>
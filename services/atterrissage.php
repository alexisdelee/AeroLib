<?php
  require_once("../platforms/databases/PDOUtils.php");

  setLocale(LC_ALL, "fr_FR");
  header("Content-Type: application/json");

  $res = [
    "message" => "",
    "description" => "atterrissage",
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

  if(isset($_POST["action"], $_POST["duration"], $_POST["matricule"], $_POST["timetable"], $_POST["email"])) {
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

    // récupération du prix hors taxes et de la tva à partir de $_POST["timetable"]
    $manager = PDOUtils::getSharedInstance();

    $remainingPlace = $manager->getAll("
      SELECT COUNT(idService) AS place FROM `service`
      WHERE description REGEXP '^atterrissage$'
        AND inscription - 5 * 60 <= ?
        AND inscription + 5 * 60 >= ?
    ", [$_POST["action"], $_POST["action"]]); // on récupère le nombre d'avions qui vont attérier à cinq minutes près

    if((int)$remainingPlace[0]["place"] >= 1) { // seul un avion ne peut attérir à la fois sur la piste
      _response_code(401, "Horraire déjà prise par un autre particulier", $res);
    }

    $plane = $manager->getAll("
      SELECT plane.idPlane, plane.base, plane.idAcoustic FROM `plane`
        LEFT JOIN `user` ON plane.idUser = user.idUser
      WHERE user.email = ?
        AND plane.matricule = ?
    ", [$_POST["email"], $_POST["matricule"]]); // on récupère des informations sur l'avion sélectionné

    if(empty($plane)) {
      _response_code(404, "Impossible de trouver les données en rapport avec votre recherche", $res);
    }

    $prestation = $manager->getAll("
      SELECT landing.* FROM `landing`
        LEFT JOIN `plane` ON landing.idModel = plane.idModel
      WHERE plane.idPlane = ?
        AND landing.timetable = ?
        AND landing.base = ?
    ", [$plane[0]["idPlane"], urldecode($_POST["timetable"]), $plane[0]["base"]]);

    if(empty($prestation)) {
      _response_code(404, "Impossible de trouver les données en rapport avec votre recherche", $res);
    }

    $minutes = getdate($_POST["action"])["hours"] * 60 + getdate($_POST["action"])["minutes"];
    if($minutes >= 360 && $minutes < 1320) {
      $acoustic = $manager->getAll("
        SELECT idAcoustic, coefficientDay FROM `acoustic`
        WHERE idAcoustic = ?
      ", [$plane[0]["idAcoustic"]]);

      $coefficient = empty($acoustic) ? -1 : floatval($acoustic[0]["coefficientDay"]); // -1 si aucun groupe acoustique n'a été trouvé
    } else {
      $acoustic = $manager->getAll("
        SELECT idAcoustic, coefficientNight FROM `acoustic`
        WHERE idAcoustic = ?
      ", [$plane[0]["idAcoustic"]]);

      $coefficient = empty($acoustic) ? -1 : floatval($acoustic[0]["coefficientNight"]);
    }

    if($coefficient == -1) {
      _response_code(404, "Impossible de trouver les données en rapport avec votre recherche", $res);
    }

    $cost_base = floatval($prestation[0]["costLanding"]) * $coefficient * ceil((int)$_POST["duration"] / (int)$prestation[0]["ratio"]); // cout de base = redevance atterrissage + coefficient groupe acoustique
    $tva_base = floatval($prestation[0]["tvaLanding"]) * $coefficient * ceil((int)$_POST["duration"] / (int)$prestation[0]["ratio"]);
    
    $remittance = $manager->getAll("
      SELECT percent FROM `remittance`
      WHERE idAcoustic = ?
    ", [$acoustic[0]["idAcoustic"]]); // capturer ceux qui ont une réduction

    if(!empty($remittance)) {
      $cost_base = $cost_base - $cost_base * floatval($remittance[0]["percent"]) / 100; // on applique la réduction
      $tva_base = $tva_base - $tva_base * floatval($remittance[0]["percent"]) / 100;
    }

    $signs = $manager->getAll("
      SELECT costSigns, tvaSigns FROM `signs`
      ORDER BY idSigns DESC
      LIMIT 1
    ");

    if($signs == -1) {
      _response_code(404, "Impossible de trouver les données en rapport avec votre recherche", $res);
    }

    $cost_base += floatval($signs[0]["costSigns"]); // on ajoute la redevance balisage
    $tva_base += floatval($signs[0]["tvaSigns"]);

    $res["inscription"] = (int)$_POST["action"];
    $res["options"]["id_plane"] = (int)$plane[0]["idPlane"];
    $res["options"]["cost"] = $cost_base; // arrondi au supéreur (journée entamée / mois entamé)
    $res["options"]["tva"] = $tva_base;
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

    $ressource["message"] = rawurlencode($message);

    echo json_encode($ressource);
    exit($error);
  }
?>
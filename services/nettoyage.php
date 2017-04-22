<?php
  require_once("../platforms/databases/PDOUtils.php");

  setLocale(LC_ALL, "fr_FR");
  header("Content-Type: application/json; charset=utf-8");

  $res = [
    "message" => "",
    "description" => "nettoyage",
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

  if(isset($_POST["action"], $_POST["matricule"], $_POST["email"])) {
    if(is_nan($_POST["action"]) || strlen($_POST["matricule"]) != 8
      || $_POST["action"] < 0) {
      _response_code(409, "Valeurs saisies incorrectes", $res);
    }

    $fullday_action = wholeDay($_POST["action"]);
    $fullday_now = wholeDay($res["subscription"]);

    if($fullday_action - $fullday_now < 48 * 3600) { // intervale de deux jours minimum
      _response_code(400, "Il faut réserver minimum 48h avant la prestation", $res);
    } else if($fullday_action - $fullday_now > 365 * 24 * 3600) { // intervale d'un an maximum
      _response_code(400, "Impossible de réserver plus d'un an avant la prestation", $res);
    }

    $manager = PDOUtils::getSharedInstance();

    $plane = $manager->getAll("
      SELECT plane.idPlane, plane.surface FROM `plane`
        LEFT JOIN `user` ON plane.idUser = user.idUser
      WHERE user.email = ?
        AND plane.matricule = ?
    ", [$_POST["email"], $_POST["matricule"]]); // on récupère des informations sur l'avion sélectionné

    if(empty($plane)) {
      _response_code(404, "Impossible de trouver les données en rapport avec votre recherche", $res);
    }

    $prestation = $manager->getAll("
      SELECT costCleaning, tvaCleaning FROM `cleaning`
      ORDER BY idCleaning DESC
      LIMIT 1
    ");

    if(empty($prestation)) {
      _response_code(404, "Impossible de trouver les données en rapport avec votre recherche", $res);
    }

    $res["inscription"] = (int)$_POST["action"];
    $res["options"]["id_plane"] = (int)$plane[0]["idPlane"];
    $res["options"]["cost"] = floatval($plane[0]["surface"]) * floatval($prestation[0]["costCleaning"]);
    $res["options"]["tva"] = floatval($plane[0]["surface"]) * floatval($prestation[0]["tvaCleaning"]);
    $res["options"]["start"] = $fullday_action;
    $res["options"]["end"] = $fullday_action + 23 * 3600 + 59 * 60 + 59; // fin de la jourée (23h59'59)

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
<?php
  require_once("../platforms/databases/PDOUtils.php");

  setLocale(LC_ALL, "fr_FR");
  header("Content-Type: application/json");

  $res = [
    "message" => "",
    "description" => "",
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

  if(isset($_POST["action"], $_POST["matricule"], $_POST["product"], $_POST["quantite"], $_POST["email"])) {
    if(is_nan($_POST["action"]) || is_nan($_POST["quantite"]) || strlen($_POST["matricule"]) != 8
      || $_POST["action"] < 0 || $_POST["quantite"] <= 0) {
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
      SELECT plane.idPlane FROM `plane`
        LEFT JOIN `user` ON plane.idUser = user.idUser
      WHERE user.email = ?
        AND plane.matricule = ?
    ", [$_POST["email"], $_POST["matricule"]]); // on récupère des informations sur l'avion sélectionné

    if(empty($plane)) {
      _response_code(404, "Impossible de trouver les données en rapport avec votre recherche", $res);
    }

    $prestation = $manager->getAll("
      SELECT idReservoir, costReservoir, tvaReservoir FROM `reservoir`
      WHERE product = ?
    ", [urldecode($_POST["product"])]); // vérification des produits sélectionnés

    if(empty($prestation)) {
      _response_code(404, "Impossible de trouver les données en rapport avec votre recherche", $res);
    }

    $res["description"] = $_POST["quantite"] . "L de " . urldecode($_POST["product"]);
    $res["inscription"] = (int)$_POST["action"];
    $res["options"]["id_plane"] = (int)$plane[0]["idPlane"];
    $res["options"]["cost"] = floatval($_POST["quantite"]) * floatval($prestation[0]["costReservoir"]);
    $res["options"]["tva"] = floatval($_POST["quantite"]) * floatval($prestation[0]["tvaReservoir"]);
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

    $ressource["message"] = rawurlencode($message);

    echo json_encode($ressource);
    exit($error);
  }
?>
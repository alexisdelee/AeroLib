<?php
  require_once("../platforms/databases/PDOUtils.php");

  setLocale(LC_ALL, "fr_FR");
  header("Content-Type: application/json; charset=utf-8");

  $res = [
    "message" => "",
    "description" => "",
    "subscription" => time(),
    "inscription" => 0,
    "options" => [
      "id_aeroclub" => "NULL",
      "cost" => 0,
      "tva" => 0,
      "start" => 0,
      "end" => 0
    ]
  ];

  if(isset($_POST["title"], $_POST["duration"], $_POST["goodWeight"], $_POST["action"], $_POST["email"])) {
    if(is_nan($_POST["action"]) || is_nan($_POST["duration"]) || is_nan($_POST["goodWeight"])
      || $_POST["action"] < 0 || $_POST["duration"] < 0 || $_POST["goodWeight"] < 0) {
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

    $activity = $manager->getAll("
      SELECT idActivity, cost, tva FROM `activity`
      WHERE title = ?
    ", [utf8_decode($_POST["title"])]);

    if(empty($activity)) { // on vérifie que la prestation existe bien
      _response_code(404, "Impossible de trouver les données en rapport avec votre recherche", $res);
    }
    
    $planes = $manager->getAll("
      SELECT type FROM `privateplane`
    "); // on récupère tous les avions de l'aéroclub

    if(empty($planes)) {
      _response_code(404, "Aucun avion disponible à cette date", $res);
    } else {
      $status = false;

      foreach($planes as $plane) { // on cherche à récupérer le premier avion disponible
        $init = curl_init();

        curl_setopt($init, CURLOPT_URL, "localhost/aerodrome/services/getStatusProperties.php");
        curl_setopt($init, CURLOPT_POST, 1);
        curl_setopt($init, CURLOPT_POSTFIELDS, http_build_query(["type" => $plane["type"], "start" => $_POST["action"], "end" => ((int)$_POST["action"] + (int)$_POST["duration"] * 60)]));
        // curl_setopt($init, CURLOPT_POSTFIELDS, http_build_query(["type" => "PIPER PA 28 180cv F-GIDI", "start" => 1493103600, "end" => 1493109000]));
        curl_setopt($init, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($init, CURLOPT_HEADER, 0);
        curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);

        $response = json_decode(curl_exec($init));

        if(curl_getinfo($init)["http_code"] == 200) { // si un avion disponible a été trouvé
          $status = true;
          break;
        }
      }

      if(!$status) {
        _response_code(404, "Aucun avion disponible à cette date", $res);
      }
    }

    if($_POST["goodWeight"] != 1) {
      _response_code(403, "Vous n'avez pas la taille minimale requise", $res);
    }

    if((int)$_POST["duration"] < 15) {
      _response_code(403, "La durée minimum est de 15 minutes pour cette prestation", $res);
    } else {
      if(preg_match("/location/i", $_POST["title"]) && (int)$_POST["duration"] > 8 * 3600) { // limite supérieure de 8h
        _response_code(403, "La durée maximum est de 8h pour cette prestation", $res);
      } else if((int)$_POST["duration"] > 2 * 3600) { // limite supérieure de 2h
        _response_code(403, "La durée maximum est de 2h pour cette prestation", $res);
      }
    }

    $idLastAeroclub = $manager->exec("
      INSERT INTO `aeroclub`(idActivity, idPrivatePlane) VALUES (?, (SELECT idPrivatePlane FROM `privateplane` WHERE type = ?))
    ", [$activity[0]["idActivity"], $plane["type"]], true);

    $res["description"] = $_POST["title"] . " de " . $_POST["duration"] . " minutes";
    $res["inscription"] = (int)$_POST["action"];
    $res["options"]["id_aeroclub"] = $idLastAeroclub;
    $res["options"]["cost"] = round((floatval($activity[0]["cost"]) * (int)$_POST["duration"]) / 60, 2);
    $res["options"]["tva"] = round((floatval($activity[0]["tva"]) * (int)$_POST["duration"]) / 60, 2);
    $res["options"]["start"] = (int)$_POST["action"];
    $res["options"]["end"] = (int)$_POST["action"] + (int)$_POST["duration"] * 60;

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
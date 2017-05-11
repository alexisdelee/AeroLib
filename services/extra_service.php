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
      "end" => 0,
      "contributions" => 0
    ]
  ];

  $initiation = false;

  if(isset($_POST["title"], $_POST["action"], $_POST["member"], $_POST["age"], $_POST["revue"], $_POST["email"])) {
    if(is_nan($_POST["action"])
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

    $activity = $manager->getAll("
      SELECT idActivity, cost, tva, `use` FROM `activity`
      WHERE title = ?
        AND formation = 1
    ", [utf8_decode($_POST["title"])]);

    if(empty($activity)) { // on vérifie que la prestation existe bien
      _response_code(404, "Impossible de trouver les données en rapport avec votre recherche", $res);
    }

    if($_POST["title"] == "sans engagement") {
      $engagement = $manager->getAll("
        SELECT service.dateEnd FROM `service`
          LEFT JOIN `receipt` ON service.idReceipt = receipt.idReceipt
          LEFT JOIN `user` ON receipt.idUser = user.idUser
          LEFT JOIN `aeroclub` ON service.idAeroclub = aeroclub.idAeroclub
          LEFT JOIN `activity` ON aeroclub.idActivity = activity.idActivity
        WHERE user.email = ?
          AND service.idAeroclub IS NOT NULL
          AND activity.title = \"sans engagement\"
        GROUP BY service.idService
        ORDER BY service.idAeroclub DESC
        LIMIT 1
      ", [$_POST["email"]]);

      if(!empty($engagement)) { // on vérifie qu'il n'a pas déjà souscrit à cette offre dans l'interval de moins d'un an
        $years = floor(($res["subscription"] - $engagement[0]["dateEnd"]) / (3600 * 24 * 365));

        if($years < 1) {
          _response_code(405, "Uniquement un vol d'initiation est disponible par an", $res);
        }
      }

      $initiation = true;
    }

    $planes = $manager->getAll("
      SELECT type, tarif_instruction FROM `privateplane`
      WHERE `use` = ?
    ", [$activity[0]["use"]]); // on récupère tous les avions de l'aéroclub

    if(empty($planes)) {
      _response_code(404, "Aucun avion disponible à cette date", $res);
    } else {
      $status = false;

      foreach($planes as $plane) { // on cherche à récupérer le premier avion disponible
        $init = curl_init();

        curl_setopt($init, CURLOPT_URL, "localhost/aerodrome/services/getStatusProperties.php");
        curl_setopt($init, CURLOPT_POST, 1);
        curl_setopt($init, CURLOPT_POSTFIELDS, http_build_query(["type" => $plane["type"], "start" => $_POST["action"], "end" => ((int)$_POST["action"] + 5 * 3600)])); // 5h de cours
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

    $idLastAeroclub = $manager->exec("
      INSERT INTO `aeroclub`(idActivity, idPrivatePlane) VALUES (?, (SELECT idPrivatePlane FROM `privateplane` WHERE type = ?))
    ", [$activity[0]["idActivity"], $plane["type"]], true);

    if($initiation) { // en cas de vol d'initiation, il ne paye pas tous les frais annexes
      $cost = 0;
      $tva = 0;
    } else {
      $cost = $planes[0]["tarif_instruction"];

      $subscription = $manager->getAll("
        SELECT service.subscription FROM `service`
          LEFT JOIN `receipt` ON service.idReceipt = receipt.idReceipt
          LEFT JOIN `user` ON receipt.idUser = user.idUser
          LEFT JOIN `aeroclub` ON service.idAeroclub = aeroclub.idAeroclub
          LEFT JOIN `activity` ON aeroclub.idActivity = activity.idActivity
        WHERE user.email = ?
          AND service.idAeroclub IS NOT NULL
          AND service.contributions = 1
        GROUP BY service.idService
        ORDER BY service.subscription DESC
        LIMIT 1
      ", [$_POST["email"]]);

      if(empty($subscription) || (!empty($subscription) && floor(($res["subscription"] - $subscription[0]["subscription"]) / (3600 * 24 * 365)) >= 1)) { // si ça fait plus d'un an, on repaye la cotisation à l'aéroclub
        if($_POST["member"] == "true") {
          $cost += 178;
          $res["options"]["contributions"] = 1;
        } else if($_POST["age"] == "true") {
          $cost += 218;
          $res["options"]["contributions"] = 1;
        }
      }

      $tva = $cost * 0.2;
      $cost -= $tva;

      $ffa = $manager->getAll("
        SELECT costFFA, tvaFFA FROM `ffa`
        WHERE revue = ?
      ", [$_POST["revue"] == "true" ? 1 : 0]); // adhésion à la Fédération Française Aéronotique

      if(!empty($ffa)) {
        $cost += $ffa[0]["costFFA"];
        $tva += $ffa[0]["tvaFFA"];
      }
    }

    $res["description"] = $_POST["title"];
    $res["inscription"] = (int)$_POST["action"];
    $res["options"]["id_aeroclub"] = $idLastAeroclub;
    $res["options"]["cost"] = round($cost + floatval($activity[0]["cost"]), 2);
    $res["options"]["tva"] = round($cost + floatval($activity[0]["tva"]), 2);
    $res["options"]["start"] = (int)$_POST["action"];
    $res["options"]["end"] = ($initiation ? (int)$_POST["action"] + 30 * 60 : (int)$_POST["action"] + 5 * 3600); // 5h de cours

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
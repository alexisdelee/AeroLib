<?php
  session_start();

  require_once("platforms/databases/PDOUtils.php");
  header("Content-Type: application/json; charset=utf-8");

  if(!isset($_POST["prestation"], $_SESSION["email"])) {
    echo json_encode(array("status" => 401, "message" => "Une authentification est nécessaire pour accéder à la ressource"));
    exit(666);
  }

  $_POST["email"] = $_SESSION["email"]; // on ajoute l'email aux POST

  $services = array(
    "atterrissage" => "localhost/aerodrome/services/atterrissage.php",
    "stationnement" => "localhost/aerodrome/services/stationnement.php",
    "avitaillement" => "localhost/aerodrome/services/avitaillement.php",
    "nettoyage" => "localhost/aerodrome/services/nettoyage.php"
  );

  if(!array_key_exists($_POST["prestation"], $services)) { // vérification de l'existence de la prestation
    echo json_encode(array("status" => 400, "message" => "Prestation inconnue"));
    exit(666);
  }

  $init = curl_init();

  curl_setopt($init, CURLOPT_URL, $services[$_POST["prestation"]]);
  curl_setopt($init, CURLOPT_POST, 1);
  curl_setopt($init, CURLOPT_POSTFIELDS, http_build_query($_POST));
  curl_setopt($init, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($init, CURLOPT_HEADER, 0);
  curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);

  $response = json_decode(curl_exec($init), true);
  $code = curl_getinfo($init)["http_code"];

  if($code == 200 && $response["options"]["id_plane"] != -1) {
    $manager = PDOUtils::getSharedInstance();

    $manager->exec("
      INSERT INTO `service`(description, subscription, inscription, dateStart, dateEnd, idReceipt, idPlane, costService, tvaService)
      VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)
    ", [
      utf8_decode($response["description"]),
      $response["subscription"],
      $response["inscription"],
      $response["options"]["start"],
      $response["options"]["end"],
      getLastReceip($_POST["email"]),
      $response["options"]["id_plane"],
      $response["options"]["cost"],
      $response["options"]["tva"]
    ]);

    $_SESSION["prestation"]["cost"] = $response["options"]["cost"];
    $_SESSION["prestation"]["tva"] = $response["options"]["tva"];
  }

  echo json_encode(array("status" => $code, "message" => $response["message"]));
  curl_close($init);

  function getLastReceip($email) {
    $manager = PDOUtils::getSharedInstance();
    $id = $manager->getAll("
      SELECT receipt.idReceipt FROM `user`
        LEFT JOIN `receipt` ON user.idUser = receipt.idUser
      WHERE user.email = ?
      ORDER BY receipt.idReceipt DESC
      LIMIT 1
    ", [$email]);

    if(empty($id)) return -1;
    else return $id[0]["idReceipt"];
  }
?>
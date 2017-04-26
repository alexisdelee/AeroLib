<?php
  require_once("../platforms/databases/PDOUtils.php");
  header("Content-Type: application/json; charset=utf-8");

  $response = [
    "message" => "",
    "type" => "",
    "reserve" => [
      "start" => 0,
      "end" => 0
    ]
  ];

  if(isset($_GET["type"], $_GET["start"], $_GET["end"])) {
    $manager = PDOUtils::getSharedInstance();

    $plane = $manager->getAll("
      SELECT idPrivatePlane FROM `privateplane`
      WHERE type = ?
    ", [$_GET["type"]]);

    if(empty($plane)) {
      $response["type"] = $_GET["type"];

      _response_code(401, "unauthorized", $response);
    }

    $planes = $manager->getAll("
      SELECT dateStart, dateEnd FROM `privateplane` AS private
        LEFT JOIN `aeroclub` ON private.idPrivatePlane = aeroclub.idPrivatePlane
      WHERE private.type = ?
        AND ((
          ? <= aeroclub.dateStart
          AND ? >= aeroclub.dateStart
            ) OR (
          ? <= aeroclub.dateEnd
          AND ? >= aeroclub.dateEnd
            ))
    ", [$_GET["type"], $_GET["start"], $_GET["end"], $_GET["start"], $_GET["end"]]);

    if(!empty($planes)) {

      $response["type"] = $_GET["type"];
      $response["reserve"] = $planes;

      _response_code(406, "unavailable", $response);
    } else {
      $response["type"] = $_GET["type"];

      _response_code(200, "ok", $response);
    }
  } else {
    _response_code(401, "unauthorized", $response);
  }

  function _response_code($error, $message, $ressource) {
    http_response_code($error);

    $ressource["message"] = $message;

    echo json_encode($ressource);
    exit($error);
  }
?>
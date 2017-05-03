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

  if(isset($_POST["type"], $_POST["start"], $_POST["end"])) {
    $manager = PDOUtils::getSharedInstance();

    $plane = $manager->getAll("
      SELECT idPrivatePlane FROM `privateplane`
      WHERE type = ?
    ", [$_POST["type"]]);

    if(empty($plane)) {
      $response["type"] = $_POST["type"];

      _response_code(401, "unauthorized", $response);
    }

    $planes = $manager->getAll("
      SELECT service.dateStart, service.dateEnd FROM `service`
        LEFT JOIN `aeroclub` ON service.idAeroclub = aeroclub.idAeroclub
      WHERE aeroclub.idAeroclub in
          (SELECT idAeroclub FROM `service`
          WHERE ((
            dateStart >= ?
            AND dateStart <= ?
              ) OR (
            dateEnd >= ?
            AND dateEnd <= ?
              ))
          )
        AND aeroclub.idPrivatePlane in
          (SELECT idPrivatePlane FROM `privateplane`
          WHERE type = ?)
      ORDER BY aeroclub.dateStart ASC
    ", [$_POST["start"], $_POST["end"], $_POST["start"], $_POST["end"], $_POST["type"]]);

    if(empty($planes)) {
      $response["type"] = $_POST["type"];

      _response_code(200, "ok", $response);
    } else {
      $response["type"] = $_POST["type"];
      $response["reserve"] = $planes;

      _response_code(406, "unavailable", $response);
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
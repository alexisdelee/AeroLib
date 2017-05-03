<?php
  require_once("init.php");

  setLocale(LC_ALL, "fr_FR");
  header("Content-Type: application/json; charset=utf-8");

  if(isset($_POST["month"]) && isset($_POST["year"])) {
    $response = shell_exec("cd bin && java -jar export.jar " . addslashes($_POST["month"]) . " " . addslashes($_POST["year"]));

    if($response != null) {
      _response_code(200, $response);
    } else {
      _response_code(400, "Mauvaise requête");
    }
  } else {
     _response_code(401, "Autorisation refusée");
  }

  function _response_code($error, $message) {
    http_response_code($error);

    echo json_encode(["status" => $error, "message" => $message]);
    exit($error);
  }
?>
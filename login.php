<?php
  session_start();

  require_once("class.user.php");

  if(isset($_POST["email"]) && isset($_POST["password"])) {
    $_POST["email"] = trim($_POST["email"]);

    unset($_SESSION["error_subscribe"]);

    $user = new User();
    $data = $user->login($_POST["email"], $_POST["password"]);
    if(gettype($data) == "array") {
      $_SESSION["accesstoken"] = $data[0];
      $_SESSION["statut"] = $data[1];
      $_SESSION["error_subscribe"][] = 0;
    } else {
      $_SESSION["error_subscribe"][] = $data;
    }
  } else {
    echo "false";
  }

  if(empty(array_filter($_SESSION["error_subscribe"]))) {
    $_SESSION["email"] = $_POST["email"];

    echo "true";
  } else {
    echo join(":", $_SESSION["error_subscribe"]);
  }
?>
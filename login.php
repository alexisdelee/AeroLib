<?php
  session_start();

  require_once("class.LogPDO.php");
  require_once("class.user.php");

  if(isset($_POST["email"]) && isset($_POST["password"])) {
    $_POST["email"] = trim($_POST["email"]);

    unset($_SESSION["error_subscribe"]);

    $user = new User();
    $_SESSION["error_subscribe"][] = $user->login($_POST["email"], $_POST["password"]);
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
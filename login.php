<?php
  require_once("init.php");

  if(isset($_POST["email"]) && isset($_POST["password"])) {
    $_POST["email"] = trim($_POST["email"]);

    unset($_SESSION["error_subscribe"]);

    $user = UserDAO::login($_POST["email"], $_POST["password"]);
    if($user === null) {
      $_SESSION["error_subscribe"][] = 5;
    } else {
      $_SESSION["error_subscribe"][] = 0;

      $_SESSION["accesstoken"] = $user->getAccesstoken();
      $_SESSION["statut"] = $user->getStatut();
    }
  } else {
    echo join(":", $_SESSION["error_subscribe"]);
  }

  if(empty(array_filter($_SESSION["error_subscribe"]))) {
    $_SESSION["email"] = $_POST["email"];

    echo "true";
  } else {
    echo join(":", $_SESSION["error_subscribe"]);
  }
?>
<?php
  session_start();

  require_once("platforms/databases/UserDAO.php");

  if(isset($_POST["name"]) && isset($_POST["age"]) && isset($_POST["email"]) && isset($_POST["password"])) {
    $_POST["name"] = trim($_POST["name"]);
    $_POST["age"] = trim($_POST["age"]);
    $_POST["email"] = trim($_POST["email"]);

    unset($_SESSION["error_subscribe"]);

    $_SESSION["error_subscribe"][] = UserDAO::statut(UserDAO::$C_EMAIL, $_POST["email"]);
    $_SESSION["error_subscribe"][] = UserDAO::statut(UserDAO::$C_NAME, $_POST["name"]);
    $_SESSION["error_subscribe"][] = UserDAO::statut(UserDAO::$C_AGE, $_POST["age"]);
  } else {
    return "false";
  }
  
  if(empty(array_filter($_SESSION["error_subscribe"]))) {
    $user = new User($_POST["name"], $_POST["password"], $_POST["email"], $_POST["age"]);
    $accesstoken = UserDAO::register($user);
    
    UserDAO::sendemail($_POST["email"], "Inscription", "Validez votre identite a l\'adresse suivante : aen.fr/verifemail.php?accesstoken=" . $accesstoken);

    echo "true";
  } else {
    echo join(":", $_SESSION["error_subscribe"]);
  }
?>
<?php
  require_once("init.php");

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

    $manager = PDOUtils::getSharedInstance();
    $manager->exec("INSERT INTO `receipt`(idUser) VALUES((SELECT idUser FROM `user` WHERE email = ?))", [$_POST["email"]]); // création d'une première facture vide

    UserDAO::sendemail($_POST["email"], "Inscription", "Validez votre identite a l\'adresse suivante : aen.fr/verifemail.php?accesstoken=" . $accesstoken);

    echo "true";
  } else {
    echo join(":", $_SESSION["error_subscribe"]);
  }
?>
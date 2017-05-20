<?php
  require_once("init.php");

  if(isset($_POST["name"]) && isset($_POST["date"]) && isset($_POST["email"]) && isset($_POST["password"])) {
    $_POST["name"] = trim($_POST["name"]);
    $_POST["date"] = trim($_POST["date"]);
    $_POST["email"] = trim($_POST["email"]);

    unset($_SESSION["error_subscribe"]);

    $_SESSION["error_subscribe"][] = UserDAO::statut(UserDAO::$C_EMAIL, $_POST["email"]);
    $_SESSION["error_subscribe"][] = UserDAO::statut(UserDAO::$C_NAME, $_POST["name"]);
    $_SESSION["error_subscribe"][] = UserDAO::statut(UserDAO::$C_BIRTHDAY, $_POST["date"]);
  } else {
    return "false";
  }

  if(empty(array_filter($_SESSION["error_subscribe"]))) {
    list($day, $month, $year) = explode("/", $_POST["date"]);
    $timestamp = mktime(0, 0, 0, $month, $day, $year);

    $user = new User($_POST["name"], $_POST["password"], $_POST["email"], $timestamp);
    $accesstoken = UserDAO::register($user);

    $manager = PDOUtils::getSharedInstance();
    $manager->exec("INSERT INTO `receipt`(idUser) VALUES((SELECT idUser FROM `user` WHERE email = ?))", [$_POST["email"]]); // création d'une première facture vide

    UserDAO::sendemail($_POST["email"], "Inscription", "Validez votre identite a l\'adresse suivante : aen.fr/verifemail.php?accesstoken=" . $accesstoken);

    echo "true";
  } else {
    echo join(":", $_SESSION["error_subscribe"]);
  }
?>
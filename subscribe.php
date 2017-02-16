<?php
  session_start();

  require_once("class.LogPDO.php");
  require_once("class.user.php");

  if(isset($_POST["name"]) && isset($_POST["age"]) && isset($_POST["email"]) && isset($_POST["password"])) {
    $_POST["name"] = trim($_POST["name"]);
    $_POST["age"] = trim($_POST["age"]);
    $_POST["email"] = trim($_POST["email"]);

    unset($_SESSION["error_subscribe"]);

    $user = new User();
    $_SESSION["error_subscribe"][] = $user->stateEmail($_POST["email"]);
    $_SESSION["error_subscribe"][] = $user->stateName($_POST["name"]);
    $_SESSION["error_subscribe"][] = $user->stageAge($_POST["age"]);
  } else {
    echo "false";
  }

  if(empty(array_filter($_SESSION["error_subscribe"]))) {
    $bdd = new LogPDO();
    $bdd->execute("INSERT INTO user(name, password, email, age, accesstoken) VALUES(?, ?, ?, ?, ?)", [utf8_decode($_POST["name"]), "test", utf8_decode($_POST["email"]), $_POST["age"], "test"]);

    echo "true";
  } else {
    echo join(":", $_SESSION["error_subscribe"]);
  }
?>
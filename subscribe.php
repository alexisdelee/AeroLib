<?php
  session_start();

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
    $accesstoken = md5(uniqid());
    
    $user = new User();
    $user->execute("INSERT INTO user(name, password, email, age, accesstoken) VALUES(?, ?, ?, ?, ?)", [utf8_decode($_POST["name"]), password_hash($_POST["password"], PASSWORD_DEFAULT), utf8_decode($_POST["email"]), $_POST["age"], $accesstoken]);

    shell_exec('cd bin/ && ./swaks --auth --server smtp.mailgun.org:587 --au postmaster@sandbox3fa628dca20c40289500f2300ae3f7db.mailgun.org --ap e5f80f40c4d61683d724d5209f3abc66 --to ' . $_POST["email"] . ' --h-Subject: "Inscription" --body "Validez votre identite a l\'adresse suivante : aen.fr/verifemail.php?accesstoken=' . $accesstoken . '"');

    echo "true";
  } else {
    echo join(":", $_SESSION["error_subscribe"]);
  }
?>
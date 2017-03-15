<?php
  session_start();

  require_once("class.user.php");

  $user = new User();
  if(isset($_SESSION["accesstoken"]) && isset($_SESSION["email"])) {
    $state = $user->isConnected($_SESSION["accesstoken"], $_SESSION["email"]);
    $_SESSION["accesstoken"] = $state;
  } else {
    $state = false;
  }

  if($state) {
    unset($_SESSION["accesstoken"]);
    unset($_SESSION["email"]);
    unset($_SESSION["statut"]);
    unset($_SESSION["error_subscribe"]);
  }

  header("Location: index.php");
?>
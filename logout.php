<?php
  session_start();

  require_once("class.user.php");

  $user = new User();
  if($user->isConnected()) {
    unset($_SESSION["accesstoken"]);
    unset($_SESSION["email"]);
    unset($_SESSION["statut"]);
  }

  header("Location: index.php");
?>
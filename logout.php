<?php
  session_start();

  require_once("class.user.php");

  $user = new User();
  if($user->isConnected()) {
    unset($_SESSION["accesstoken"]);
    unset($_SESSION["email"]);
  }

  header("Location: .");
?>
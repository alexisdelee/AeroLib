<?php
  require_once("init.php");

  if($router->state) {
    /* unset($_SESSION["accesstoken"]);
    unset($_SESSION["email"]);
    unset($_SESSION["statut"]);
    unset($_SESSION["private_key"]);
    unset($_SESSION["error_subscribe"]);
    unset($_SESSION["localisation"]); */

    unset($_SESSION);
    session_destroy();
  }

  header("Location: index.php");
?>
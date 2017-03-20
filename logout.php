<?php
  session_start();

  require_once("platforms/databases/UserDAO.php");

  if(isset($_SESSION["accesstoken"]) && isset($_SESSION["email"])) {
    if(UserDAO::isConnected($_SESSION["accesstoken"], $_SESSION["email"])) {
      unset($_SESSION["accesstoken"]);
      unset($_SESSION["email"]);
      unset($_SESSION["statut"]);
      unset($_SESSION["private_key"]);
      unset($_SESSION["error_subscribe"]);

      session_destroy();
    }
  }

  header("Location: index.php");
?>

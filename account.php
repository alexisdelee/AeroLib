<?php
  session_start();

  require_once("platforms/databases/UserDAO.php");
  require_once("nav.php");
  require_once("popup.php");

  if(isset($_SESSION["accesstoken"]) && isset($_SESSION["email"])) {
    $state = UserDAO::isConnected($_SESSION["accesstoken"], $_SESSION["email"]);
    $_SESSION["accesstoken"] = $state;
  } else {
    $state = false;
  }

  if(!$state) {
    header("Location: index.php");
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Compte</title>
    <link rel="stylesheet" type="text/css" href="style/account.css">
    <link rel="icon" type="image/png" href="res/logo.png">
  </head>
  <body>
    
  </body>
</html>
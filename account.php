<?php
  session_start();

  require_once("class.user.php");
  require_once("nav.php");

  $user = new User();
  if(!$user->isConnected()) {
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
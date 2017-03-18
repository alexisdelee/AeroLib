<?php
  session_start();

  if(isset($_SESSION["statut"]) && $_SESSION["statut"] == 2) {
    shell_exec("cd bin/ && sh daemonWeather.sh");
    header("Location: localisation.php");
  } else {
    header("Location: index.php");
  }
?>
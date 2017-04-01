<?php
  require_once("init.php");
  // session_start();

  if($router->permission == 2) {
    shell_exec("cd bin/ && ./daemonWeather.sh");
    header("Location: localisation.php");
  } else {
    header("Location: index.php");
  }
?>

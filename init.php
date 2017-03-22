<?php
  session_start();

  require_once("platforms/databases/UserDAO.php");
  require_once("controllers/Router.php");

  Router::$allowAreas = [
    "escale",
    "aeroclub",
    "account",
    "localisation",
    "weatherService",
    "logout",
    "verifemail",
    "phpmyadmin"
  ];

  Router::$exceptions = [
    "root" => [
      "escale" => "phpmyadmin.php",
      "aeroclub" => "phpmyadmin.php",
      "account" => "phpmyadmin.php"
    ],
    "user" => [
      "phpmyadmin" => "escale.php",
    ]
  ];

  Router::access(pathinfo($_SERVER["REQUEST_URI"], PATHINFO_FILENAME));
?>

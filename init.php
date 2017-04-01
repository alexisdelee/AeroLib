<?php
  session_start();

  require_once("platforms/databases/UserDAO.php");
  require_once("controllers/Router.php");

  $router = new Router();

  $router->allowAreas = [
    "escale",
    "aeroclub",
    "account",
    "localisation",
    "weatherService",
    "logout",
    "verifemail",
    "phpmyadmin",
    "phptopdf",
    "lab"
  ];

  $router->exceptions = [
    "root" => [
      "escale" => "phpmyadmin.php",
      "aeroclub" => "phpmyadmin.php",
      "account" => "phpmyadmin.php"
    ],
    "user" => [
      "phpmyadmin" => "escale.php",
    ]
  ];

  $router->access(pathinfo($_SERVER["REQUEST_URI"], PATHINFO_FILENAME));
?>
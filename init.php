<?php
  session_start();

  require_once("platforms/databases/UserDAO.php");
  require_once("controllers/Router.php");

  $router = new Router();

  $router->allowAreas = [
    "escale",
    "aeroclub",
    "basket",
    "account",
    "localisation",
    "weatherService",
    "logout",
    "verifemail",
    "phpmyadmin",
    "phptopdf"
  ];

  $router->exceptions = [
    "root" => [
      "escale" => "phpmyadmin.php",
      "aeroclub" => "phpmyadmin.php",
      "basket" => "phpmyadmin.php",
      "account" => "phpmyadmin.php"
    ],
    "user" => [
      "phpmyadmin" => "escale.php",
    ]
  ];

  $router->access(pathinfo($_SERVER["REQUEST_URI"], PATHINFO_FILENAME));
?>
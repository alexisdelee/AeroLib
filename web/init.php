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
    "user" => [
      "phpmyadmin" => "escale.php",
    ],
    "root" => [
      "escale" => "phpmyadmin.php",
      "aeroclub" => "phpmyadmin.php",
      "basket" => "phpmyadmin.php",
      "account" => "phpmyadmin.php"
    ],
    "staff" => [
      "escale" => "account.php",
      "aeroclub" => "account.php",
      "basket" => "account.php",
      "localisation" => "account.php",
      "weatherService" => "account.php",
      "phpmyadmin" => "account.php",
      "phptopdf" => "account.php"
    ]
  ];

  $router->access(pathinfo($_SERVER["REQUEST_URI"], PATHINFO_FILENAME));
?>
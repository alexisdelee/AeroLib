<?php
  class Router {
    public static $allowAreas = null;
    public static $exceptions = null;
    public static $state = false;
    public static $permission = 0;

    public static function access($location) {
      if(isset($_SESSION["accesstoken"],
               $_SESSION["email"],
               $_SESSION["statut"])) {
        $_SESSION["accesstoken"] = UserDAO::isConnected($_SESSION["accesstoken"], $_SESSION["email"]);
        Router::$state = $_SESSION["accesstoken"] === false ? false : true;
        Router::$permission = $_SESSION["statut"];
      } else {
        Router::$state = false;
      }

      if($location === "index" || 
        ((array_search($location, ["login", "subscribe"]) !== false) && Router::ajaxRequest())) {
        // do something
        // $_SESSION["localisation"] = $location . ".php";
      } else {
        if(!Router::$state){
          header("Location: index.php");
        } else if(Router::deniedAccess($location)) {
          // header("Location: " . $lastLocation);
          header("Location: index.php");
        }

        if(Router::$exceptions) { // redirections
          foreach(Router::$exceptions[Router::$permission == 2 ? "root" : "user"] as $key_ => $exception) {
            if($location === $key_) {
              /* if($location !== "phpmyadmin") {
                $_SESSION["localisation"] = $location . ".php";
              } */
              
              header("Location: " . $exception);
            }
          }
        }
      }
    }

    public static function deniedAccess($location) {
      if(Router::$allowAreas){
        foreach(Router::$allowAreas as $area) {
          if($location === $area || Router::ajaxRequest()) {
            return false;
          }
        }

        return true;
      }

      return false;
    }

    public static function ajaxRequest() {
      if(isset($_SERVER["CONTENT_TYPE"]) && $_SERVER["CONTENT_TYPE"] === "application/x-www-form-urlencoded") {
        return true;
      } else {
        return false;
      }
    }
  }
?>

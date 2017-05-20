<?php
  class Router {
    public $allowAreas = null;
    public $exceptions = null;
    public $state = false;
    public $permission = 0;

    public function Router() {}

    public function access($location) {
      if(isset($_SESSION["accesstoken"],
               $_SESSION["email"],
               $_SESSION["statut"])) {
        $_SESSION["accesstoken"] = UserDAO::isConnected($_SESSION["accesstoken"], $_SESSION["email"]);
        $this->state = $_SESSION["accesstoken"] === false ? false : true;
        $this->permission = $_SESSION["statut"];
      } else {
        $this->state = false;
      }

      if($location === "index" || 
        ((array_search($location, ["login", "subscribe"]) !== false) && $this->ajaxRequest())) {
        // do something
        // $_SESSION["localisation"] = $location . ".php";
      } else {
        if(!$this->state){
          header("Location: index.php");
        } else if($this->deniedAccess($location)) {
          // header("Location: " . $lastLocation);
          header("Location: index.php");
        }

        if($this->exceptions) { // redirections
          foreach($this->exceptions[$this->permission == 2 ? "root" : ($this->permission == 3 ? "staff" : "user")] as $key_ => $exception) {
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

    private function deniedAccess($location) {
      if($this->allowAreas){
        foreach($this->allowAreas as $area) {
          if($location === $area || $this->ajaxRequest()) {
            return false;
          }
        }

        return true;
      }

      return false;
    }

    private function ajaxRequest() {
      if(isset($_SERVER["CONTENT_TYPE"]) && $_SERVER["CONTENT_TYPE"] === "application/x-www-form-urlencoded") {
        return true;
      } else {
        return false;
      }
    }

    public function rewriteUrl($key, $value) {
      require_once("libs/php/build_http_url.php");

      $url = parse_url($_SERVER["REQUEST_URI"]);
      if(!empty($url["query"])) {
        parse_str($url["query"], $params); // analyse l'url
      }

      $params[$key] = $value; // on récrit la valeur si elle existe déjà, sinon on l'a crée

      $url["path"] = basename($url["path"]);
      $url["query"] = http_build_query($params);
      return implode("?", $url);
    }
  }
?>
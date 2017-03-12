<?php
  // session_start();
  
  require_once("class.LogPDO.php");

  class User {
    public function __construct() {}

    public function MySQLAccess($key = null, $value = null) {
      $bdd = new LogPDO();
      $result = $bdd->execute("SELECT * FROM user WHERE " . $key . " = ?", [$value]);

      if(empty($result)) {
        return false;
      } else {
        return true;
      }
    }

    public function login($email, $pwd) {
      $bdd = new LogPDO();
      $result = $bdd->execute("SELECT password, accesstoken, statut FROM user WHERE email = ? AND statut <> 0", [$email]);

      if(!empty($result) && password_verify($pwd, $result[0]["password"])) {
        $_SESSION["accesstoken"] = $result[0]["accesstoken"];
        $_SESSION["statut"] = $result[0]["statut"];
        
        return 0;
      } else {
        return 5;
      }
    }

    public function stateEmail($email) {
      if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 1;
      } else if($this->MySQLAccess("email", $email)) {
        return 2;
      } else {
        return 0;
      }
    }

    public function stateName($name) {
      if(strlen($name) < 2) {
        return 3;
      } else {
        return 0;
      }
    }

    public function stageAge($age) {
      $int = intval($age);

      if($int >= 10 && $int <= 115) {
        return 0;
      } else {
        return 4;
      }
    }

    public function generateAccessToken($email) {
      $accesstoken = md5(uniqid($email));

      $bdd = new LogPDO();
      $bdd->execute("UPDATE user SET accesstoken = ? WHERE email = ?", [$accesstoken, $email]);

      return $accesstoken;
    }

    public function isConnected() {
      if(!isset($_SESSION["accesstoken"]) || empty($_SESSION["accesstoken"])) {
        return false;
      }

      $bdd = new LogPDO();
      $result = $bdd->execute("SELECT id FROM user WHERE email = ? AND accesstoken = ?", [$_SESSION["email"], $_SESSION["accesstoken"]]);

      if(empty($result)) {
        return false;
      } else {
        $_SESSION["accesstoken"] = $this->generateAccessToken($_SESSION["email"]);
        return true;
      }
    }
  }
?>
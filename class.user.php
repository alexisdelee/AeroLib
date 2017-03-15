<?php  
  require_once("class.LogPDO.php");

  class User extends LogPDO {
    public function __construct($hostbdd = "localhost", $namebdd = "aerodrome", $userbdd = "root", $mdpbdd = "") {
      parent::__construct($hostbdd, $namebdd, $userbdd, $mdpbdd);
    }

    public function MySQLAccess($key = null, $value = null) {
      $result = parent::execute("SELECT * FROM user WHERE " . $key . " = ?", [$value]);

      if(empty($result)) {
        return false;
      } else {
        return true;
      }
    }

    public function login($email, $pwd) {
      $result = parent::execute("SELECT password, accesstoken, statut FROM user WHERE email = ? AND statut <> 0", [$email]);

      if(!empty($result) && password_verify($pwd, $result[0]["password"])) {
        
        return [$result[0]["accesstoken"], $result[0]["statut"]];
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

      parent::execute("UPDATE user SET accesstoken = ? WHERE email = ?", [$accesstoken, $email]);

      return $accesstoken;
    }

    public function isConnected($accesstoken, $email) {
      if(!isset($email) || !isset($accesstoken) || empty($accesstoken)) {
        return false;
      }

      $result = parent::execute("SELECT id FROM user WHERE email = ? AND accesstoken = ?", [$email, $accesstoken]);

      if(empty($result)) {
        return false;
      } else {
        return $this->generateAccessToken($email);
      }
    }
  }
?>
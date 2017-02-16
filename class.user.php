<?php
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
      $result = $bdd->execute("SELECT password FROM user WHERE email = ? AND statut <> 0", [$email]);

      if(!empty($result) && password_verify($pwd, $result[0]["password"])) {
        return true;
        // header("Location: index.php");
      } else {
        return false;
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
  }
?>
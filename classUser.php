<?php
  require_once("LogPDO.php");

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
  }
?>
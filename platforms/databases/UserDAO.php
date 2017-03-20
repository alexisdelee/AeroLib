<?php
  require_once("PDOUtils.php");
  require_once(__DIR__ . "/../../controllers/User.php");
  require_once(__DIR__ . "/../../controllers/Authentification.php");

  class UserDAO { // Database Access Object
    public static $C_EMAIL = 0x01;
    public static $C_NAME  = 0x02;
    public static $C_AGE   = 0x04;

    public static function register(User $user) {
      $manager = PDOUtils::getSharedInstance();
      $params = [
        utf8_decode($user->getName()),
        UserDAO::passwordManager($user->getPassword()),
        utf8_decode($user->getEmail()),
        $user->getAge(),
        $accesstoken = UserDAO::accesstokenManager()
      ];

      $manager->exec("INSERT INTO `user` (name, password, email, age, accesstoken) VALUES (?, ?, ?, ?, ?)", $params);
      return $accesstoken;
    }

    public static function statut($state, $value) {
      $manager = PDOUtils::getSharedInstance();

      if($state & self::$C_EMAIL) {
        $result = $manager->getAll("SELECT email FROM `user` WHERE email = ?", [$value]);

        if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
          return 1;
        } else if(!empty($result)) {
          return 2;
        } else {
          return 0;
        }
      } else if($state & self::$C_NAME) {
        if(strlen($value) < 2) {
          return 3;
        } else {
          return 0;
        }
      } else if($state & self::$C_AGE) {
        $int = intval($value);

        if($int >= 10 && $int <= 115) {
          return 0;
        } else {
          return 4;
        }
      }

      return 0;
    }

    public static function passwordManager($password, $strict = false) {
      if($strict){
        if(strlen($password) >= 6) {
          return password_hash(substr($password, 0, 6), PASSWORD_DEFAULT);
        } else {
          return password_hash(str_pad($password, 6), PASSWORD_DEFAULT);
        }
      } else {
        return password_hash($password, PASSWORD_DEFAULT);
      }
    }

    public static function accesstokenManager($token = null) {
      if($token !== null)
        return md5(uniqid($token));
      else
        return md5(uniqid());
    }

    public static function sendemail($to, $subject, $body, $attachment = null) {
      if($attachment === null) {
        $command = "cd bin/ && ./swaks --auth ";
        $command .= "--server smtp.mailgun.org:587 ";
        $command .= "--au postmaster@sandbox3fa628dca20c40289500f2300ae3f7db.mailgun.org ";
        $command .= "--ap " . Authentification::api("smtp") . " ";
        $command .= "--to \"" . $to . "\" ";
        $command .= "--h-Subject: \"" . utf8_decode($subject) . "\" ";
        $command .= "--body \"" . utf8_decode($body) . "\"";
      } else {
        $command = "cd bin/ && cat \"../" . $attachment["path"] . "\" | ./swaks --auth ";
        $command .= "--server smtp.mailgun.org:587 ";
        $command .= "--au postmaster@sandbox3fa628dca20c40289500f2300ae3f7db.mailgun.org ";
        $command .= "--ap " . Authentification::api("smtp") . " ";
        $command .= "--to \"" . $to . "\" ";
        $command .= "--h-Subject: \"" . utf8_decode($subject) . "\" ";
        $command .= "--body \"" . utf8_decode($body) . "\" ";
        $command .= "--attach-type text/plain ";
        $command .= "--attach-name \"" . $attachment["name"] . "\" ";
        $command .= "--attach -";
      }

      shell_exec($command);
    }

    public static function login($email, $password) {
      $manager = PDOUtils::getSharedInstance();
      $params = [
        $email
      ];

      $result = $manager->getAll("SELECT password, accesstoken, statut FROM `user` WHERE email = ? AND statut <> 0", $params);

      if(!empty($result) && password_verify($password, $result[0]["password"])) {
        $row = $result[0];

        return new User(
          null,
          $password,
          $email,
          null,
          $row["accesstoken"],
          $row["statut"]
        );
      }

      return null;
    }

    public static function generateAccesstoken($email) {
      $accesstoken = UserDAO::accesstokenManager($email);

      $manager = PDOUtils::getSharedInstance();
      $manager->exec("UPDATE `user` SET accesstoken = ? WHERE email = ?", [$accesstoken, $email]);

      return $accesstoken;
    }

    public static function isConnected($accesstoken, $email) {
      if(!isset($email) || !isset($accesstoken) || empty($accesstoken)) {
        return false;
      }

      $manager = PDOUtils::getSharedInstance();
      $params = [
        $email,
        $accesstoken
      ];

      $result = $manager->getAll("SELECT idUser FROM `user` WHERE email = ? AND accesstoken = ?", [$email, $accesstoken]);

      if(!empty($result)) {
        return UserDAO::generateAccesstoken($email);
      } else {
        return false;
      }
    }
  }
?>

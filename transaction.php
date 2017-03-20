<?php
  session_start();

  require_once("platforms/databases/UserDAO.php");
  require_once("controllers/Authentification.php");

  if(isset($_SESSION["email"]) && isset($_POST["type"])) {
    if($_POST["type"] == "demand" && isset($_POST["amount"])) {
      $_SESSION["private_key"] = UserDAO::accesstokenManager(); // générer une clé privée temporaire

      $key = Authentification::_xor(
        hash("crc32", $_POST["amount"]),
        hash("crc32", $_SESSION["private_key"]),
        hash("crc32", $_SESSION["email"])
      );

      UserDAO::sendemail($_SESSION["email"], "Confirmation transfère d'argent", "Pour terminer votre transaction, saisissez " . $key . " dans le formulaire en bas de page");
    } else if(isset($_SESSION["private_key"]) && $_POST["type"] == "verification" && isset($_POST["key"]) && isset($_POST["amount"])) {
      $hash = Authentification::_xor(
        $_POST["key"],
        hash("crc32", $_SESSION["email"]),
        hash("crc32", $_SESSION["private_key"])
      );

      if($hash == hash("crc32", $_POST["amount"])) {
        $manager = PDOUtils::getSharedInstance();
        $manager->exec("UPDATE `user` SET credit = ? WHERE email = ?", [$_POST["amount"], $_SESSION["email"]]);

        unset($_SESSION["private_key"]); // destruction de la clé

        echo "true";
      }
    }

    echo "false";
  } else {
    header("Location: index.php");
  }
?>

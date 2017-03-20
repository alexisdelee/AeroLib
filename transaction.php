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

      file_put_contents("account/" . $_SESSION["email"] . ".txt", $key);
      UserDAO::sendemail(
        $_SESSION["email"],
        "Confirmation transfère d'argent",
        "Pour terminer votre transaction de " . $_POST["amount"] . " euro(s), saisissez le code fourni en pièce jointe dans le formulaire en bas de page.",
        [
          name => "code.txt",
          path => "account/" . $_SESSION["email"] . ".txt"
        ]
      );
    } else if(isset($_SESSION["private_key"]) && $_POST["type"] == "verification" && isset($_POST["key"]) && isset($_POST["amount"])) {
      $hash = Authentification::_xor(
        $_POST["key"],
        hash("crc32", $_SESSION["email"]),
        hash("crc32", $_SESSION["private_key"])
      );

      if($hash == hash("crc32", $_POST["amount"])) {
        $manager = PDOUtils::getSharedInstance();
        $manager->exec("UPDATE `user` SET credit = credit + ? WHERE email = ?", [$_POST["amount"], $_SESSION["email"]]);

        unset($_SESSION["private_key"]); // destruction de la clé
        unlink("account/" . $_SESSION["email"] . ".txt"); // suppression du fichier pour libérer de la place

        echo "true";
        exit(666);
      }
    }

    echo "false";
  } else {
    header("Location: index.php");
  }
?>

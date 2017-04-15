<?php
  require_once("init.php");

  if(isset($_POST["plane"])) {
    $data = json_decode($_POST["plane"], true);

    // vérification matricule
    if(strlen($data["matricule"]) != 8) {
      echo "Le matricule doit être composé de 8 caractères.";
      exit(666);
    }

    $manager = PDOUtils::getSharedInstance();
    $result = $manager->getAll("
      SELECT idPlane 
      FROM `plane` 
      WHERE matricule = ?
    ", [$data["matricule"]]);

    if(!empty($result)) {
      echo "Ce matricule est déjà enregistré.";
      exit(666);
    }

    // vérification longueur et envergure
    $data["length"] = floatval($data["length"]);
    $data["width"] = floatval($data["width"]);

    if($data["length"] < 2 || $data["length"] > 20 || $data["width"] < 2 || $data["width"] > 20) {
      echo "La longueur et/ou l'envergure de l'appareil ne correspondent pas aux normes de l'aérodrome (entre 2m et 20m).";
      exit(666);
    } else {
      $surface = $data["length"] * $data["width"];
    }

    // vérification masse maximale
    $data["mass"] = floatval($data["mass"]);

    if($data["mass"] < 20 || $data["mass"] > 10000) {
      echo "La masse de l'appareil ne correspond pas aux normes de l'aérodrome (entre 20kg et 10000kg).";
      exit(666);
    }

    // vérification modèle
    $idModel = $manager->getAll("
      SELECT idModel
      FROM `model`
      WHERE typeModel = ?
    ", [utf8_decode($data["model"])]);

    if(empty($idModel)) {
      echo "Ce modèle n'est pas référencé dans la base de données de l'aérodrome.";
      exit(666);
    }

    // vérification groupe acoustique
    $idAcoustic = $manager->getAll("
      SELECT idAcoustic
      FROM `acoustic`
      WHERE groupAcoustic = ?
    ", [$data["acoustic"]]);

    if(empty($idAcoustic)) {
      echo "Ce groupe acoustisque n'est pas référencé dans la base de données de l'aérodrome.";
      exit(666);
    }

    // tout est bon, on n'a plus qu'à stocker dans la BDD
    $manager->exec("
      INSERT INTO `plane`
      (matricule, surface, mass, idModel, idAcoustic, idUser)
      VALUES(?, ?, ?, ?, ?, (SELECT idUser FROM `user` WHERE email = ?))
    ", [$data["matricule"], $surface, $data["mass"], $idModel[0]["idModel"], $idAcoustic[0]["idAcoustic"], $_SESSION["email"]]);

    echo "ok";
  }
?>
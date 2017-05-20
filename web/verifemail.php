<?php
	require_once("init.php");

  if(isset($_GET["accesstoken"]) && strlen($_GET["accesstoken"]) == 32) {
    $manager = PDOUtils::getSharedInstance();
    $email = $manager->getAll("SELECT email FROM `user` WHERE accesstoken = ?", [$_GET["accesstoken"]]);
    if(!empty($email)) {
    	$manager->exec("UPDATE `user` SET statut = 1 WHERE accesstoken = ?", [$_GET["accesstoken"]]);

    	$_SESSION["accesstoken"] = $_GET["accesstoken"];
    	$_SESSION["email"] = $email[0]["email"];
      $_SESSION["statut"] = 1;
    }
  }

  header("Location: index.php");
?>

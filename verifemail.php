<?php
	session_start();
	
	require_once("class.LogPDO.php");

  if(isset($_GET["accesstoken"]) && strlen($_GET["accesstoken"]) == 32) {
    $bdd = new LogPDO();
    $email = $bdd->execute("SELECT email FROM user WHERE accesstoken = ?", [$_GET["accesstoken"]]);
    if(!empty($email)) {
    	$bdd->execute("UPDATE user SET statut = 1 WHERE accesstoken = ?", [$_GET["accesstoken"]]);
    	
    	$_SESSION["accesstoken"] = $_GET["accesstoken"];
    	$_SESSION["email"] = $email[0]["email"];
      $_SESSION["statut"] = 1;
    }
  }
  
  header("Location: index.php");
?>

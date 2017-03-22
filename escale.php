<?php
  require_once("init.php");
  require_once("nav.php");
  require_once("popup.php");
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Services clients</title>
    <link rel="stylesheet" type="text/css" href="style/escale.css">
    <link rel="icon" type="image/png" href="res/logo.png">
  </head>
  <body>
    <article>
      <center>
        <p>
          <div id="container">
            <?php
              $manager = PDOUtils::getSharedInstance();
              $prestations = [
                [
                  "table" => "category",
                  "title" => "Atterrissage",
                  "path" => "landing.png"], 
                [
                  "table" => "reservoir",
                  "title" => "Avitaillement",
                  "path" => "reservoir.png"],
                [
                  "table" => "area",
                  "title" => "Stationnement",
                  "path" => "area.png"],
                [
                  "table" => "cleaning",
                  "title" => "Nettoyage",
                  "path" =>"cleaning.png"]
              ];

              $credit = $manager->getAll("SELECT credit FROM `user` WHERE email = ?", [$_SESSION["email"]]);
              $isCredit = (int)$credit[0]["credit"] > 0 ? true : false;

              foreach($prestations as $prestation) {
                $data = $manager->getAll("SELECT id" . ucfirst($prestation["table"]) . " FROM " . $prestation["table"]);

                if($isCredit && !empty($data[0]["id" . ucfirst($prestation["table"])])) {
                  echo "<a title=\"" . $prestation["title"] . "\" href=\"#" . $prestation["table"] . "\"><div class=\"prestations available\"><img style=\"width: 100%; height: 100%;\" src=\"res/prestations/" . $prestation["path"] . "\"></div></a>";
                } else {
                  echo "<a title=\"" . $prestation["title"] . "\" href=\"#" . $prestation["table"] . "\"><div class=\"prestations\"><img style=\"width: 100%; height: 100%; opacity: 0.2; cursor: default;\" src=\"res/prestations/" . $prestation["path"] . "\"></div></a>";
                }
              }
            ?>
          </div>
        </p>
      </center>
    </article>
  </body>
</html>

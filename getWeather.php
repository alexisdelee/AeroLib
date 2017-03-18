<?php
  require_once("platforms/databases/UserDAO.php");

  $manager = PDOUtils::getSharedInstance();
  $data = $manager->getAll("SELECT * FROM weather ORDER BY idWeather DESC LIMIT 1");

  $items = ["date", "description", "temp", "pressure", "humidity", "temp_min", "temp_max", "visibility", "speed", "sunrise", "sunset"];

  if(count($data) > 0) {
    foreach($items as $item){
      echo $item . "-" . $data[0][$item] . ":";
    }
  } else {
    echo "undefined";
  }
?>
<?php
  require_once("class.LogPDO.php");

  $bdd = new LogPDO();
  $result = $bdd->execute("SELECT * FROM meteo ORDER BY id DESC LIMIT 1");

  $items = ["date", "description", "temp", "pressure", "humidity", "temp_min", "temp_max", "visibility", "speed", "sunrise", "sunset"];

  if($data = $result[0]) {
    foreach($items as $item){
      echo $item . "-" . $data[$item] . ":";
    }
  } else {
    echo "undefined";
  }
?>
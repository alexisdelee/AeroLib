<?php
  ini_set("display_errors", 1);

  $bd = new PDO("mysql:dbname=aerodrome;host=localhost", "root", "");
  $result = $bd->query("SELECT * FROM meteo ORDER BY id DESC LIMIT 1");

  $items = ["date", "description", "temp", "pressure", "humidity", "temp_min", "temp_max", "visibility", "speed", "sunrise", "sunset"];

  if($data = $result->fetch()){
    foreach($items as $item){
      echo $item . "-" . $data[$item] . ":";
    }
  } else {
    echo "undefined";
  }
?>
<?php
  require_once("init.php");

  if(isset($_POST["type"])) {
    if($_POST["type"] === "load") {
      if(isset($_POST["type"], $_POST["table"], $_POST["column"], $_POST["id"])) {
        $manager = PDOUtils::getSharedInstance();
        $data = $manager->getAll("SELECT idLanding, timetable FROM " . $_POST["table"] . " WHERE " . $_POST["column"] . " = ?", [$_POST["id"]]);
        
        foreach($data as $array) {
          echo utf8_encode(implode(":", $array)) . "\n";
        }
      }
    } else if($_POST["type"] === "insert") {
      foreach ($_POST as $post) {
        if(!isset($post)) {
          exit(666);
        }
      }

      array_shift($_POST); // supprime le premier element du tableau
      $where = array_pop($_POST);

      $manager = PDOUtils::getSharedInstance();
      /* $manager->exec("INSERT INTO " .
        array_shift($_POST)
        . "(" . 
        implode(", ", array_keys($_POST))
        . ") VALUES(" .
        implode(" ", array_fill(0, count($_POST), "?"))
        . ")", array_map(function($value) {
          $v = explode(":", $value);
          if($v[1] === "string") {

            return "\"" . $v[0] . "\"";
          } else {
            return $v[0];
          }
        }, array_values($_POST))); */

      return $manager->exec("INSERT INTO " .
        array_shift($_POST)
        . "(" . 
        implode(", ", array_keys($_POST))
        . ") VALUES(" .
        implode(", ", array_map(function($value) {
          $v = explode(":", $value);
          return "\"" . utf8_decode($v[0]) . "\"";
        }, array_values($_POST)))
        . ")");
    } else if($_POST["type"] === "update" && isset($_POST["where"])) {
      foreach ($_POST as $post) {
        if(!isset($post)) {
          exit(666);
        }
      }

      array_shift($_POST); // supprime le premier element du tableau
      $where = array_pop($_POST);

      $request = "UPDATE " . array_shift($_POST) . " SET ";

      foreach($_POST as $key => $post) {
        $request .= $key . " = ?";
      }

      $manager = PDOUtils::getSharedInstance();
      $manager->exec($request . " WHERE " . $where, array_values($_POST));
    } else if($_POST["type"] === "query" && isset($_POST["query"])) {
      $params = explode(" ", $_POST["query"]);

      foreach($params as $key => $param) {
        if(strlen($param) >= 2 && $param[1] === "$") {
          if(isset($_SESSION[substr($param, 2, strlen($param) - 3)])) {
            $params[$key] = "\"" . $_SESSION[substr($param, 2, strlen($param) - 3)] . "\"";
          } else {
            exit(666);
          }
        }
      }

      $params = implode(" ", $params);

      $manager = PDOUtils::getSharedInstance();
      $data = $manager->getAll($params)[0];

      if(!empty($data)) {
        echo implode(":", $data);
      } else {
        echo null;
      }
    }
  }
?>
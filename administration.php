<?php
  require_once("class.LogPDO.php");

  if(!isset($_POST["action"])) {
    header("Location: index.php");
  }

  if($_POST["action"] == "select" && isset($_POST["table"])) {
    $bdd = new LogPDO();

    echo "<table border=\"1\">";
    echo "<tr>";

    $columns = eachSelect($bdd->execute("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA` = \"aerodrome\" AND `TABLE_NAME` = \"" . $_POST["table"] . "\"", []), null, function($value) {
      echo "<th>" . strtoupper($value) . "</th>";
    }, null);

    echo "</tr>";

    eachSelect($bdd->execute("SELECT * FROM " . $_POST["table"] . " ORDER BY id DESC", []), function() {
      echo "<tr>";
    }, function($value, $key, $num) use($columns) {
      if($key != "id" && $key != "accesstoken") {
        echo "<td contentEditable=\"true\" class=\"data\" data-table=\"" . $columns[$num] . "\">";
      } else {
        echo "<td data-table=\"" . $columns[$num] . "\">";
      }

      if($key == "password" || $key == "accesstoken") {
        echo "********************";
      } else {
        echo utf8_encode($value);
      }

      echo "</td>";
    }, function() {
      echo "</tr>";
    });

    echo "<tr>";

    for($input = 0, $limit = count($columns); $input < $limit; $input++) {
      if(!$input) {
        echo "<td>&#60;GENERATED&#62;</td>";
      } else {
        echo "<td><input style=\"width: 100%;\" type=\"text\"></td>";
      }
    }

    echo "</tr>";
    echo "</table>";
  } else if($_POST["action"] == "update" && isset($_POST["table"]) && isset($_POST["column"]) && isset($_POST["value"]) && isset($_POST["id"])) {
    $bdd = new LogPDO();

    if($_POST["column"] == "password") {
      $bdd->execute("UPDATE " . $_POST["table"] . " SET " . $_POST["column"] . " = ? WHERE id = ?", [password_hash($_POST["value"], PASSWORD_DEFAULT), $_POST["id"]]);
    } else {
      $bdd->execute("UPDATE " . $_POST["table"] . " SET " . $_POST["column"] . " = ? WHERE id = ?", [$_POST["value"], $_POST["id"]]);
    }
  }

  function eachSelect($request, $headerCallback, $contentCallback, $footerCallback) {
    $values = [];

    foreach($request as $key => $value) {
      if($headerCallback != null) $headerCallback($value, $key);

      $num = 0; // reset numeration

      foreach($request[$key] as $key_ => $value_) {
        if(is_numeric($key_)) {
          continue;
        }

        $values[] = $value_;

        if($contentCallback != null) $contentCallback($value_, $key_, $num++);
      }

      if($footerCallback != null) $footerCallback($value, $key);
    }

    return $values;
  }
?>
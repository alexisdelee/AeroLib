<?php
  require_once("class.LogPDO.php");

  if(!isset($_POST["action"])) {
    header("Location: index.php");
  }

  if($_POST["action"] == "select" && isset($_POST["table"])) {
    select_db($_POST["table"]);
  } else if($_POST["action"] == "update" && isset($_POST["table"]) && isset($_POST["column"]) && isset($_POST["value"]) && isset($_POST["id"])) {
    update_db($_POST["table"], $_POST["column"], $_POST["value"], $_POST["id"]);
  } else if($_POST["action"] == "insert" && isset($_POST["table"]) && isset($_POST["columns"]) && isset($_POST["values"])) {
    insert_db($_POST["table"], $_POST["columns"], $_POST["values"]);
  }

  function select_db($table) {
    $bdd = new LogPDO();

    echo "<table border=\"1\">";
    echo "<tr>";

    $columns = eachSelect($bdd->execute("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA` = \"aerodrome\" AND `TABLE_NAME` = \"" . $table . "\"", []), null, function($value) {
      echo "<th>" . strtoupper($value) . "</th>";
    }, null);

    echo "</tr>";

    eachSelect($bdd->execute("SELECT * FROM " . $table . " ORDER BY id DESC", []), function() {
      echo "<tr>";
    }, function($value, $key, $num) use($columns) {
      if($key != "id" && $key != "accesstoken") {
        echo "<td contentEditable=\"true\" class=\"data\" data-column=\"" . $columns[$num] . "\">";
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
        echo "<td><input style=\"width: 100%;\" type=\"text\" data-column=\"" . $columns[$input] . "\"></td>";
      }
    }

    echo "</tr>";
    echo "</table>";
  }

  function update_db($table, $column, $value, $id) {
    $bdd = new LogPDO();

    if($column == "password") {
      $password = passwordManager($value);

      $bdd->execute("UPDATE " . $table . " SET " . $column . " = ? WHERE id = ?", [password_hash($password, PASSWORD_DEFAULT), $id]);
    } else {
      $bdd->execute("UPDATE " . $table . " SET " . $column . " = ? WHERE id = ?", [$value, $id]);
    }
  }

  function insert_db($table, $columns, $values) {
    $columns = explode("##", $columns);
    $values = explode("##", $values);

    $secret = array_fill(0, count($columns), "?");

    if(($key = array_search("accesstoken", $columns)) !== false){
      $values[$key] = md5(uniqid($values[$key]));
    }

    if(($key = array_search("password", $columns)) !== false){
      $values[$key] = password_hash(passwordManager($values[$key]), PASSWORD_DEFAULT);
    }

    $bdd = new LogPDO();
    $bdd->execute("INSERT INTO " . $table . "(" . implode(",", $columns) . ") VALUES(" . implode(",", $secret) . ")", array_merge($values));
  }

  function passwordManager($password) {
    if(strlen($password) > 6) {
        return substr($password, 0, 6);
      } else {
        return $password . str_repeat(" ", 6 - strlen($password));
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
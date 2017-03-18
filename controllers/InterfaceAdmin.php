<?php
  require_once(__DIR__ . "/../platforms/databases/UserDAO.php");

  class InterfaceAdmin {
    public static function draw($table) {
      $manager = PDOUtils::getSharedInstance();

      $information = $manager->getAll("SELECT `COLUMN_NAME`, `DATA_TYPE` 
                                FROM `INFORMATION_SCHEMA`.`COLUMNS` 
                                WHERE `TABLE_SCHEMA` = \"aerodrome\" AND `TABLE_NAME` = \"" . $table . "\"");

      $columns = InterfaceAdmin::filterHeader("COLUMN_NAME", $information);
      InterfaceAdmin::drawHeader($columns);

      $data = $manager->getAll("SELECT * FROM " . $table . " ORDER BY id" . ucfirst($table) . " DESC");
      while(($cells = each($data))) {
        InterfaceAdmin::drawContent($cells["value"]);
      }

      $types = InterfaceAdmin::filterHeader("DATA_TYPE", $information);
      InterfaceAdmin::drawFooter($columns, $types);
    }

    public static function update($table, $column, $value, $id) {
      switch($column) {
        case "password":
          $value = UserDAO::passwordManager($value, true);
          break;
        case "accesstoken":
          $value = UserDAO::accesstokenManager($value);
          break;
        default:
          $value = utf8_decode($value);
      }

      $manager = PDOUtils::getSharedInstance();
      $manager->exec("UPDATE " . $table . " SET " . $column . " = ? WHERE id" . ucfirst($table) . " = ?", [$value, $id]);
    }

    public static function insert($table, $columns, $values) {
      $columns = explode("##", $columns);
      $values = explode("##", $values);

      $secret = array_fill(0, count($columns), "?");

      foreach($values as $key => $value) {
        switch($columns[$key]) {
          case "password":
            $values[$key] = UserDAO::passwordManager($value, true);
            break;
          case "accesstoken":
            $values[$key] = UserDAO::accesstokenManager($value);
            break;
          default:
            $values[$key] = utf8_decode($value);
        }
      }

      $manager = PDOUtils::getSharedInstance();
      $manager->exec("INSERT INTO " . $table . "(" . implode(",", $columns) . ") VALUES(" . implode(",", $secret) . ")", array_merge($values));
    }

    public static function filterHeader($filter, $arr) {
      $array = [];

      foreach($arr as $value) {
        $array[] = array_intersect_key($value, array_flip(array($filter)));
      }

      $array = array_column($array, $filter);

      return $array;
    }

    public static function exceptionDraw($column, $cell) {
      if(substr($column, 0, 2) == "id") {
        return "<td data-table=\"" . $column . "\">" . $cell . "</td>";
      } else {
        return "<td contentEditable=\"true\" class=\"data\" data-column=\"" . $column . "\">"
          . ($column == "password" || $column == "accesstoken" ? "********************" : utf8_encode($cell)) .
          "</td>";
      }
    }

    public static function drawHeader($columns) {
      echo "<table border=\"1\"><tr>";

      foreach($columns as $column) 
        echo "<th>" . strtoupper($column) . "</th>";

      echo "</tr>";
    }

    public static function drawContent($cells) {
      echo "<tr>";

      foreach($cells as $column => $cell) {
        echo InterfaceAdmin::exceptionDraw($column, $cell);
      }

      echo "</tr>";
    }

    public static function drawFooter($columns, $types) {
      echo "<tr>";
      
      foreach($columns as $key => $column) {
        if($key == 0) {
          echo "<td>" . htmlspecialchars("<GENERATED>") . "</td>";
        } else {
          echo "<td><input style=\"width: 100%;\" type=\"text\" data-column=\"" . $column . "\" placeholder=\"" . $types[$key] . "\"></td>";
        }
      }

      echo "</tr></table>";
    }
  }
?>
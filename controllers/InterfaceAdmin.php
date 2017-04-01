<?php
  require_once(__DIR__ . "/../platforms/databases/UserDAO.php");

  class InterfaceAdmin {
    public function __construct() {}

    public function draw($table) {
      $manager = PDOUtils::getSharedInstance();

      $information = $manager->getAll("SELECT `COLUMN_NAME`, `DATA_TYPE`
                                       FROM `INFORMATION_SCHEMA`.`COLUMNS`
                                       WHERE `TABLE_SCHEMA` = \"aerodrome\" AND `TABLE_NAME` = \"" . $table . "\"");

      $columns = $this->filterHeader("COLUMN_NAME", $information);
      $this->drawHeader($columns);

      $data = $manager->getAll("SELECT * FROM " . $table . " ORDER BY id" . ucfirst($table) . " DESC");
      while(($cells = each($data))) {
        $this->drawContent($cells["value"]);
      }

      $types = $this->filterHeader("DATA_TYPE", $information);
      $this->drawFooter($columns, $types);
    }

    public function update($table, $column, $value, $id) {
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

    public function insert($table, $columns, $values) {
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

    private function filterHeader($filter, $arr) {
      $array = [];

      foreach($arr as $value) {
        $array[] = array_intersect_key($value, array_flip(array($filter)));
      }

      $array = array_column($array, $filter);

      return $array;
    }

    private function exceptionDraw($column, $cell, $id) {
      if($id) {
        return "<td data-table=\"" . $column . "\">" . $cell . "</td>";
      } else {
        return "<td contentEditable=\"true\" class=\"data\" data-column=\"" . $column . "\">"
          . ($column == "password" || $column == "accesstoken" ? "********************" : utf8_encode($cell)) .
          "</td>";
      }
    }

    private function drawHeader($columns) {
      echo "<table border=\"1\"><tr>";

      foreach($columns as $column)
        echo "<th>" . strtoupper($column) . "</th>";

      echo "</tr>";
    }

    private function drawContent($cells) {
      echo "<tr>";

      $start = true;
      foreach($cells as $column => $cell) {
        echo $this->exceptionDraw($column, $cell, $start);

        if($start) {
          $start = false;
        }
      }

      echo "</tr>";
    }

    private function drawFooter($columns, $types) {
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
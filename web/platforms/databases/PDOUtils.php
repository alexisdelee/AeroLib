<?php
  class PDOUtils {
    private $pdo_;
    private static $sharedInstance_;

    private function __construct() {
      $this->pdo_ = new PDO("mysql:host=localhost;dbname=aerodrome", "root", "");
    }

    public static function getSharedInstance() {
      if(!isset(PDOUtils::$sharedInstance_)) {
        PDOUtils::$sharedInstance_ = new PDOUtils();
      }

      return PDOUtils::$sharedInstance_;
    }

    public function getAll($sql, $params = null) {
      $statement = $this->pdo_->prepare($sql);
      if($statement && $statement->execute($params)) {
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        unset($statement);
        return $result;
      }

      return null;
    }

    public function exec($sql, $params = null, $insertOption = false) {
      $statement = $this->pdo_->prepare($sql);
      if($statement && $statement->execute($params)) {
        unset($statement);

        if($insertOption) return $this->pdo_->lastInsertId();
        else return true;
      }

      return false;
    }
  }
?>
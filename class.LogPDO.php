<?php
  class LogPDO {
    private $hostbdd_;
    private $namebdd_;
    private $userbdd_;
    private $mdpbdd_;
    private $connection_;

    public function __construct($hostbdd = "localhost", $namebdd = "aerodrome", $userbdd = "root", $mdpbdd = "") {
      $this->hostbdd_ = $hostbdd;
      $this->namebdd_ = $namebdd;
      $this->userbdd_ = $userbdd;
      $this->mdpbdd_ = $mdpbdd;

      $this->connection_ = $this->connect_();
    }

    private function connect_() {
      try {
        $bdd = new PDO("mysql:dbname=" . $this->namebdd_ . ";host=" . $this->hostbdd_, $this->userbdd_, $this->mdpbdd_);
      } catch(Exception $e) {
        die("error: " . $e->getMessage());
      }

      return $bdd;
    }

    public function execute($command = null, $var = []) {
      $query = $this->connection_->prepare($command);
      $query->execute($var);

      return $query->fetchAll();
    }
  }
?>
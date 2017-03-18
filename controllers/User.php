<?php
  class User {
    private $name_;
    private $password_;
    private $email_;
    private $age_;
    private $accesstoken_;
    private $statut_;

    public function __construct($name = null, $password = null, $email = null, $age = 666, $accesstoken = null, $statut = 1) {
      $this->name_ = $name;
      $this->password_ = $password;
      $this->email_ = $email;
      $this->age_ = $age;
      $this->accesstoken_ = $accesstoken;
      $this->statut_ = $statut;
    }

    public function getName() { return $this->name_; }
    public function getPassword() { return $this->password_; }
    public function getEmail() { return $this->email_; }
    public function getAge() { return $this->age_; }
    public function getAccesstoken() { return $this->accesstoken_; }
    public function getStatut() { return $this->statut_; }
  }
?>
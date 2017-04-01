<?php
  require_once("init.php");
  require_once("controllers/InterfaceAdmin.php");

  if(!isset($_POST["action"])) {
    header("Location: index.php");
  }

  $interface = new InterfaceAdmin();

  switch($_POST["action"]) {
    case "select":
      if(isset($_POST["table"]))
        $interface->draw($_POST["table"]);

      break;
    case "update":
      if(isset($_POST["table"], $_POST["column"], $_POST["value"], $_POST["id"]))
        $interface->update($_POST["table"], $_POST["column"], $_POST["value"], $_POST["id"]);

      break;
    case "insert":
      if(isset($_POST["table"], $_POST["columns"], $_POST["values"]))
        $interface->insert($_POST["table"], $_POST["columns"], $_POST["values"]);

      break;
  }
?>
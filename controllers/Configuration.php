<?php
  class Configuration {
    public static function api($apifile) {
      return file_get_contents("/../config/" . $apifile . ".key", FILE_USE_INCLUDE_PATH);
    }
  }
?>
<?php
  class Authentification {
    public static function api($apifile) {
      return file_get_contents(__DIR__ . "/../config/" . $apifile . "_api.key", FILE_USE_INCLUDE_PATH);
    }

    public static function _xor() {
      $numargs = func_num_args();
      $arg_list = func_get_args();

      $hash = str_repeat(" ", 8);

      if($numargs) {
        for($i = 0; $i < 8; $i++) {
          $hash[$i] = $arg_list[0][$i];
          for($arg = 1; $arg < $numargs; $arg++) {
            $hash[$i] = $hash[$i] ^ $arg_list[$arg][$i];
            $hash[$i] = ~$hash[$i];
          }
        }

        return $hash;
      } else {
        return null;
      }
    }
  }
?>
<?php
  find("./controllers", 95);

  function find($dir, $illegal_character) {
    $cdir = scandir($dir);
    foreach ($cdir as $key => $file) {
      if(!in_array($file, [".", ".."])) {
        if(is_dir($dir . "/" . $file)) {
          find($dir . "/" . $file, $illegal_character);
        } else {
          analyze($dir . "/" . $file, $illegal_character);
        }
      }
    }
  }

  function analyze($file, $illegal_character) {
    $handle = @fopen($file, "r");
    $characters = [];

    if($handle) {
      $line = 1;

      $characters[] = $file;

      while(($buffer = fgets($handle, 4096)) !== false) {
        for($c = 0; $c < strlen($buffer); $c++) {
          if(ord($buffer[$c]) == $illegal_character) {
           $characters[] = " (Line " . $line . ", Column " . ($c + 1) . ")";
          }
        }

        $line++;
      }

      var_dump($characters);

      fclose($handle);
    }
  }
?>
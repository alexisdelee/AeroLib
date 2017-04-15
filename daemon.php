<?php
  exec("find /var/www/aerodrome/account/* -mtime +1 -exec rm {} \;"); // supprime tous les fichiers datant de plus d'une journée

  
?>
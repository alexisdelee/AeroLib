<?php
  require_once("platforms/databases/UserDAO.php");

  exec("find /var/www/aerodrome/account/* -mtime +1 -exec rm {} \;"); // supprime tous les fichiers datant de plus d'une journée

  $manager = PDOUtils::getSharedInstance();
  $receipts = $manager->getAll("
    SELECT receipt.idReceipt AS id
    FROM `service`
      LEFT JOIN `receipt` ON service.idReceipt = receipt.idReceipt
    WHERE service.inscription <= ?
      AND receipt.isPaid = 0
      AND receipt.creation <> 0
      AND receipt.idAdministrative IS NULL
  ", [time()]);

  if(!empty($receipts)) {
    foreach($receipt as $receipts) {
      $manager->exec("
        SELECT @id := idAdministrative, @cost := costAdministrative, @tva := tvaAdministrative
        FROM `administrative`
        ORDER BY idAdministrative DESC
        LIMIT 1;

        UPDATE `receipt`
        SET idAdministrative = @id,
            totalCost = totalCost + @cost,
            totalTva = totalTva + @tva
        WHERE idReceipt = ?;
      ", [$receipt["id"]]);
    }
  }
?>
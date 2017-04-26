<?php
  require_once("init.php");
  require_once("libs/fpdf/fpdf.php");

  class PDF extends FPDF {
    // Tableau simple
    function BasicTable($header, $data, $footer, $penality) {
      // En-tête
      $this->SetFillColor(34, 34, 34);
      $this->SetTextColor(255);
      $this->SetDrawColor(78, 124, 173);

      foreach($header as $key => $col) {
        if($key == 1) {
          $this->Cell(103, 7, $col, 1, 0, "C", true);
        } else {
          $this->Cell(22, 7, $col, 1, 0, "C", true);
        }
      }

      $this->Ln();

      // Données
      $this->SetFillColor(224, 235, 255);
      $this->SetTextColor(0);

      foreach($data as $line) {
        foreach($line as $key => $row) {
          if($key == 1) {
            $_length = ceil(strlen($row) / 66);
            $this->Cell(103, 6 * ($_length), $row, 1, 0, "C", false);
          } else {
            $this->Cell(22, 6, $row, 1, 0, "C", false);
          }
        }

        $this->Ln();
      }

      // Pénalité
      if($penality[0]["costAdministrative"] != null) {
        $this->SetFillColor(34, 34, 34);
        $this->SetTextColor(255);
        $this->SetDrawColor(78, 124, 173);

        $this->Cell(75, 6, "", 0, 0, "C", false);
        $this->Cell(50, 6, "P" . chr(233) . "nalit" . chr(233), 1, 0, "C", true);

        $this->SetTextColor(166, 24, 53);

        $this->Cell(22, 6, number_format($penality[0]["costAdministrative"], 2, ",", " ") . chr(128), 1, 0, "C", false);
        $this->Cell(22, 6, number_format($penality[0]["tvaAdministrative"], 2, ",", " ") . chr(128), 1, 0, "C", false);
        $this->Cell(22, 6, number_format($penality[0]["costAdministrative"] + $penality[0]["tvaAdministrative"], 2, ",", " ") . chr(128), 1, 0, "C", false);

        $this->Ln();
      }

      // Prix total
      $this->SetFillColor(34, 34, 34);
      $this->SetTextColor(255);
      $this->SetDrawColor(78, 124, 173);

      $this->Cell(75, 6, "", 0, 0, "C", false);
      $this->Cell(50, 6, "Total", 1, 0, "C", true);

      $this->SetTextColor(78, 124, 173);

      $this->Cell(22, 6, $footer[0], 1, 0, "C", false);
      $this->Cell(22, 6, $footer[1], 1, 0, "C", false);
      $this->Cell(22, 6, $footer[2], 1, 0, "C", false);
    }
  }

  if(isset($_GET["id"])) {
    $manager = PDOUtils::getSharedInstance();
    $data = $manager->getAll("
      SELECT receipt.idReceipt, receipt.creation, receipt.totalCost, receipt.totalTva, service.idService, service.description, service.subscription, service.dateStart, service.dateEnd, service.costService, service.tvaService
      FROM `service`
        LEFT JOIN `receipt` ON receipt.idReceipt = service.idReceipt
      WHERE receipt.idReceipt = ?
        AND receipt.idUser =
          (SELECT idUser
          FROM `user`
          WHERE email = ?)
    ", [$_GET["id"], $_SESSION["email"]]);

    if(empty($data)) {
      header("Location: index.php");
    } else {
      $pdf = new PDF();

      $pdf->AddPage();
      $pdf->setTitle("Facture");

      $pdf->Image("res/logo.png", 2, -5, 50);
      $pdf->SetFont("Arial", "B", "20");
      $pdf->Cell(180, 10, utf8_decode("Aérodrome d'Évreux-Normandie"), 0, 1, "R");
      $pdf->SetFont("Arial", "", "10");
      $pdf->Cell(180, 0, utf8_decode("École de pilotage avion / ulm - Vol decouverte et initiation"), 0, 1, "R");

      $pdf->SetFont("Arial", "B", "20");
      $pdf->Cell(0, 100, utf8_decode("Facture n°" . $data[0]["idReceipt"] . " du " . date("d/m/Y", $data[0]["creation"]) . " à " . date("H:i:s", $data[0]["creation"])), 0, 1, "C");
      $pdf->SetFont("Arial", "", "10");

      $header = ["Ref.", "D" . chr(233) . "tails", "HT", "TVA", "TTC"];
      $values = [];

      foreach($data as $value) {
        $values[] = [$value["idService"], ucfirst($value["description"]), number_format($value["costService"], 2, ",", " ") . chr(128), number_format($value["tvaService"], 2, ",", " ") . chr(128), number_format(floatval($value["costService"]) + floatval($value["tvaService"]), 2, ",", " ") . chr(128)];
      }

      // pénalité
      $penality = $manager->getAll("
        SELECT administrative.costAdministrative, administrative.tvaAdministrative
        FROM `receipt`
          LEFT JOIN `administrative` ON receipt.idAdministrative = administrative.idAdministrative
        WHERE receipt.idReceipt = ?
      ", [$_GET["id"]]);

      $pdf->SetFont("Arial", "", 10);
      $pdf->BasicTable($header, $values, [number_format($data[0]["totalCost"], 2, ",", " ") . chr(128), number_format($data[0]["totalTva"], 2, ",", " ") . chr(128), number_format($data[0]["totalCost"] + $data[0]["totalTva"], 2, ",", " ") . chr(128)], $penality);

      $user = $manager->getAll("
        SELECT name FROM `user`
        WHERE email = ?
      ", [$_SESSION["email"]]);

      $pdf->SetFont("Arial", "", "10");
      $pdf->SetTextColor(0);
      $pdf->Cell(-190, -125, utf8_decode("à destination de Madame/Monsieur ") . (empty($user) ? $_SESSION["email"] : $user[0]["name"]), 0, 1, "C");

      $pdf->Output();
    }
  }
?>
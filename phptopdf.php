<?php
  require_once("init.php");
  require_once("libs/fpdf/fpdf.php");

  class PDF extends FPDF {
    // Tableau simple
    function BasicTable($header, $data, $footer) {
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
            // $this->MultiCell(103, 6, $row, 1, "C", false);
          } else {
            $this->Cell(22, 6, $row, 1, 0, "C", false);
            // $this->MultiCell(22, 6, $row, 1, "C", false);
          }
        }

        $this->Ln();
      }

      // Prix total
      $this->SetFillColor(34, 34, 34);
      $this->SetTextColor(255);
      $this->SetDrawColor(78, 124, 173);

      $this->Cell(75, 6, "Total", 0, 0, "C", false);
      $this->Cell(50, 6, "Total", 1, 0, "C", true);

      $this->SetTextColor(78, 124, 173);

      $this->Cell(22, 6, $footer[0], 1, 0, "C", false);
      $this->Cell(22, 6, $footer[1], 1, 0, "C", false);
      $this->Cell(22, 6, $footer[2], 1, 0, "C", false);
    }

    function removeAccents($str) {
      $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή');
      $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');
      
      return str_replace($a, $a, $str);
    }
  }

  if(isset($_GET["id"])) {
    $manager = PDOUtils::getSharedInstance();
    $data = $manager->getAll("
      SELECT receipt.creation, receipt.totalCost, receipt.totalTva, service.idService, service.description, service.subscription, service.dateStart, service.dateEnd, service.costService, service.tvaService
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
      $pdf->Cell(180, 10, "A" . chr(233) . "rodrome d'" . chr(201) . "vreux-Normandie", 0, 1, "R");
      $pdf->SetFont("Arial", "", "10");
      $pdf->Cell(180, 0, chr(201) . "cole de pilotage avion / ulm - Vol decouverte et initiation", 0, 1, "R");

      $pdf->SetFont("Arial", "B", "20");
      $pdf->Cell(0, 100, "Facture du " . date("d/m/Y", $data[0]["creation"]) . " " . chr(224) . " " . date("H:i:s", $data[0]["creation"]), 0, 1, "C");
      $pdf->SetFont("Arial", "", "10");

      $header = ["Ref.", "D" . chr(233) . "tails", "HT", "TVA", "TTC"];
      $values = [];

      foreach($data as $value) {
        $values[] = [$value["idService"], ucfirst($pdf->removeAccents($value["description"])), number_format($value["costService"], 2, ",", " ") . chr(128), number_format($value["tvaService"], 2, ",", " ") . chr(128), number_format(floatval($value["costService"]) + floatval($value["tvaService"]), 2, ",", " ") . chr(128)];
      }

      $pdf->SetFont("Arial", "", 10);
      $pdf->BasicTable($header, $values, [number_format($data[0]["totalCost"], 2, ",", " ") . chr(128), number_format($data[0]["totalTva"], 2, ",", " ") . chr(128), number_format($data[0]["totalCost"] + $data[0]["totalTva"], 2, ",", " ") . chr(128)]);

      $pdf->Output();
    }
  }
?>
<?php
  require_once("init.php");
  require_once("nav.php");
  require_once("popup.php");
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Panier</title>
    <link rel="icon" type="image/png" href="res/logo.png">
    <link rel="stylesheet" type="text/css" href="style/popup.css">
    <link rel="stylesheet" type="text/css" href="style/account.css">
  </head>
  <body>
    <section>
      <center>
        <p>
          Faites un clic droit sur une des prestations ci-dessous pour afficher plus de détails sur cette dernière.
        </p>
      </center>
    </section>

    <article id="historique">
      <center>
        <?php
          $manager = PDOUtils::getSharedInstance();
          $data = $manager->getAll("
            SELECT service.idService, service.description, service.subscription, service.costService, service.tvaService, service.dateStart, service.dateEnd
            FROM service 
              LEFT JOIN receipt ON service.idReceipt = receipt.idReceipt 
            WHERE receipt.idUser =
              (SELECT idUser
              FROM `user`
              WHERE email = ?)
              AND isPaid = 0
            ORDER BY service.idService DESC
          ", [$_SESSION["email"]]);

          if(empty($data)) {
            echo "<i style=\"color: #A61835;\">Aucun service dans votre panier...</i>";
          } else {
            echo "<ul>";

            foreach($data as $value) {
              echo "<input type=\"checkbox\" id=\"service-" . $value["idService"] . "\"><label oncontextmenu=\"service(" . $value["idService"] . "); return false;\" for=\"service-" . $value["idService"] . "\"><small><strong>[" . date("d/m/Y H:i:s", $value["subscription"]) . "]</strong></small> " . utf8_encode($value["description"]) . "</label>";
              echo "<br>
                    <div style=\"display: none; width: 100%; margin: 20px 0;\" class=\"description\" id=\"description-" . $value["idService"] . "\">
                      <table>
                        <tr>
                          <th>Date de début</th>
                          <th>Date de fin</th>
                          <th>HT</th>
                          <th>TVA</th>
                          <th>TTC</th>
                        </tr>
                        <tr>
                          <td>" . date("d/m/Y H:i:s", $value["dateStart"]) . "</td>
                          <td>" . date("d/m/Y H:i:s", $value["dateEnd"]) . "</td>
                          <td>" . number_format(floatval($value["costService"]), 2, ",", " ") . "€</td>
                          <td>" . number_format(floatval($value["tvaService"]), 2, ",", " ") . "€</td>
                          <td>" . number_format(floatval($value["costService"]) + floatval($value["tvaService"]), 2, ",", " ") . "€</td>
                        </tr>
                      </table>
                    </div>";
            }

            echo "</ul>";
            echo "<button id=\"accept\">Valider le panier</button>";
            echo "<button id=\"cancel\">Supprimer</button>";
          }
        ?>
      </center>
    </article>

    <script type="text/javascript" src="controllers/oXHR.js"></script>
    <script type="text/javascript" src="controllers/Request.js"></script>
    <script type="text/javascript" src="app.popup.js"></script>
    <script type="text/javascript">
      // informations supplémentaires sur le panier
      let lastIdService = -1;
      function service(idService) {
        if(lastIdService == idService) {
          document.querySelector("#description-" + idService).style.display = "none";
        } else {
          let nodesArray = [].slice.call(document.querySelectorAll(".description"))
          for(let node of nodesArray) {
            if(~node.id.indexOf("description-" + idService)) {
              document.querySelector("#description-" + idService).style.display = "inline-block";
            } else {
              node.style.display = "none";
            }
          }
        }

        lastIdService = idService;
      }

      // supprimer une ou plusieurs prestations
      let services = document.querySelectorAll("input[type=\"checkbox\"]");
      let servicesChecked = [];
      for(let service of services) {
        service.addEventListener("click", (e) => {
          if(e.target.checked) {
            servicesChecked.push(e.target.id);
          } else {
            servicesChecked.splice(servicesChecked.indexOf(e.target.id), 1);
          }
        });
      }

      let cancel = document.querySelector("#cancel");
      if(cancel !== null) {
        cancel.addEventListener("click", () => {
          let request = new Request();

          for(let index = 0, n = servicesChecked.length; index < n; index++) {
            let _id = servicesChecked[index].split("-")[1];
            request.post("service.php", "type=remove&service=" + _id, () => {
              if(index == n - 1) {
                window.location.reload();
              }
            });
          }
        });
      }

      // accepter toutes les prestations
      let accept = document.querySelector("#accept");
      if(accept) {
        accept.addEventListener("click", () => {
          let request = new Request();

          request.post("service.php", "type=add", (response) => {
            if(response === "ok") {
              window.location.reload();
            } else {
              popup.manager.open("<span style=\"color: #A61835\">" + response + "</span>");
            }
          });
        });
      }
    </script>
  </body>
</html>
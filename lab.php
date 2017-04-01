<?php require_once("init.php"); ?>

<!DOCTYPE html>
  <html>
  <head>
    <title></title>
    <meta charset="utf-8">
  </head>
  <body>
    <div id="date">
      <input id="date" type="text" placeholder="28/03/2017 11:36">
      <button id="send">Soumettre</button>
      <button id="confirm">Confirmation</button>
    </div><br>

    <select id="acoustic">
      <option>Groupe acoustique</option>
      <?php
        $manager = PDOUtils::getSharedInstance();
        $result = $manager->getAll("SELECT groupAcoustic FROM `acoustic`");

        foreach($result as $data) {
          echo "<option>" . $data["groupAcoustic"] . "</option>";
        }
      ?>
    </select>

    <select id="model">
      <option>Type d'avion</option>
    </select>
    
    <div id="forfait">
      <ul></ul>
      <button>Ajouter au panier</button>
    </div>

    <script type="text/javascript" src="libs/moment/moment.js"></script>
    <script type="text/javascript" src="libs/moment/moment-timezone.js"></script>
    <script type="text/javascript" src="libs/moment/moment-ferie-fr.js"></script>
    <script type="text/javascript" src="controllers/oXHR.js"></script>
    <script type="text/javascript" src="controllers/MomentUtils.js"></script>
    <script type="text/javascript" src="controllers/Request.js"></script>
    <script type="text/javascript">
      let state = 1;
      let date = null;
      let data = {
        date: 0,
        prestation: null,
        state: null,
        acoustic: null,
        model: null,
        service: null,
        duration: 1
      };

      let send_input = document.querySelector("#send");
      let confirm_input = document.querySelector("#confirm");
      let acoustic_container = document.querySelector("#acoustic");
      let model_container = document.querySelector("#model");
      let option_timetable = document.querySelector("#forfait ul");
      let valid_timetable = document.querySelector("#forfait button");

      send_input.addEventListener("click", (e) => {
        if(state & 1) {
          state = 2;

          let dates = e.target.parentNode.children[0].value;
          return landing(dates);
        }
      });

      confirm_input.addEventListener("click", (e) => {
        if(state & 1) {
          state = 256;

          let dates = e.target.parentNode.children[0].value;
          return landing(dates);
        }
      });

      acoustic_container.addEventListener("change", (e) => {
        let acoustic = e.target.selectedOptions[0].textContent;
        data.acoustic = acoustic;
      });

      model_container.addEventListener("change", (e) => {
        state = 8;

        let model = e.target.selectedOptions[0].textContent;
        data.model = model;
        landing(model);
      });

      valid_timetable.addEventListener("click", (e) => {
        if(!(state & 8)) {
          alert("Veuillez remplir le formulaire entièrement.");
          return;
        }

        let options = e.target.parentNode.children[0].children;
        for(let option of options) {
          if(option.children[0].checked) {
            data.service = option.children[0].textContent;

            let labels = document.querySelectorAll("label");
            for(let label of labels) {
              if(label.htmlFor === option.children[0].id) {
                data.service = label.dataset.timetable;

                if(data.service === "Unité") {
                  let duration = parseInt(option.childNodes[option.childNodes.length - 1].value);
                  if(isNaN(duration) || duration < 2 || duration > 100) { // limiter à 2 jours minimum et 100 jours maximum
                    data.duration = 2;
                  } else {
                    data.duration = duration;
                  }
                }
              }
            }

            break;
          }
        }

        if(~Object.values(data).indexOf(null)) { // vérifie si tous les champs ont été renseignés
          alert("Veuillez remplir le formulaire entièrement.");
          return;
        }

        data.date = (new Date()).timestamp();
        
        state = 16;
        landing();
      });

      function landing(option) {
        if(state & 2) { // vérification format date et abonnement déjà existant
          let manager = new MomentUtils();
          date = manager.localeToUTC(moment(option, "DD/MM/YYYY HH:mm"), "Europe/Paris"); // décalage horaire

          if(date !== null) {
            if(manager.limiter(date, moment(new Date()).add(30, "hours")._d, true) // supérieur à 30h
            && manager.limiter(date, moment(new Date()).add(1, "year")._d, false)) { // inférieur à 1 an
              let request = new Request();

              data.prestation = manager.timestamp(date);

              let hour = parseInt(moment(moment(data.prestation * 1000)).format("HH"));
              if(hour >= 6 && hour < 22) {
                data.state = "day";
              } else {
                data.state = "night";
              }

              request.post("getInformation.php", "get=forfait&date=" + data.prestation, (response) => {
                if(response === "unavailable") {
                  state = 128;
                } else if(response === "no-forfait") {
                  state = 4;
                } else {
                  state = 64;
                }

                return landing();
              });
            } else {
              alert("Date non comprise entre 30h et 1 an");
            }
          } else {
            alert("Date invalide");
          }
        } else if(state & 4) { // on analyse le modèle et le groupe acoustique
          let request = new Request();
          request.post("getInformation.php", "get=model", (response) => {
            if(response !== "undefined") {
              for(let model of response.split(":")) {
                add_list(model_container, model);
              }
            } else {
              alert("Erreur");
            }
          });
        } else if(state & 8) { // on analyse la timetable
          let manager = new MomentUtils();
          let timetable = date.getType();
          let id_time = null;

          switch(timetable) {
            case "week":
              id_time = "Semaine";
              break;
            default:
              id_time = "Week-end/JF";
          }

          let request = new Request();
          request.post("getInformation.php", "timetable=" + id_time + "&model=" + option, (response) => {
            if(response !== "undefined") {
              for(let child of option_timetable.childNodes) {
                option_timetable.removeChild(child); // on vide le ul
              }

              for(let r = 0, res = response.split("#"); r < res.length; r++) {
                let infos = res[r].split(":");

                if(infos.length > 1) {
                  let status = infos.shift(); // on stocke le status
                  if(status === "1") {
                    id_time = infos.shift(); // on supprime le timetable
                    let abonnement = infos.pop(); // on supprime l'indice sur le type de forfait

                    add_option(option_timetable, "<input id=\"landing-" + r + "\" name=\"landing\" type=\"radio\">" + "<label data-timetable=\"" + id_time + "\" for=\"landing-" + r + "\">" + (abonnement === "1" ? "<small>[abonnement]</small> " : "") + id_time + " " + infos.map((value, index) => {
                      return value + "\u20AC " + ["HT", "TVA"][index];
                    }).join(" | ") + "</label> " + ((id_time === "Unité") ? " <input placeholder=\"2 journées par défaut\">" : ""));
                  }
                }
              }
            }
          });
        } else if(state & 16) { // envoie des données pour inscription
          let request = new Request();
          request.post("getInformation.php", "type=landing&domain=register&data=" + JSON.stringify(data), (response) => {
            if(response === "undefined") {
              alert("Erreur interne");
            } else {
              let prices = response.split(":");
              prices = prices.map((value) => {
                return Math.round(parseFloat(value) * 100) / 100;
              });

              state = 32;
              return landing(Math.round(prices.reduce((a, b) => {
                return a + b;
              }) * 100) / 100);
            }
          });
        } else if(state & 32) {
          state = 1; // reset
          alert("Cette prestation a été ajoutée à votre panier.");
        } else if(state & 64) { // finalisation de la facture
          state = 1; // reset
          alert("Vous avez bien été enregistré(e) pour le " + moment(date.UTCToLocale("Europe/London")).format("DD/MM/YYYY à HH:mm") + ".\nMerci de bien vouloir confirmer votre venu minimum 24h avant votre atterrissage.");
        } else if(state & 128) {
          alert("Impossible d'attérir à cette heure-là, l'emplacement est déjà réservé.");
        } else if(state & 256) {
          data.date = (new Date()).timestamp();

          let request = new Request();
          request.post("getInformation.php", "type=landing&domain=update&data=" + JSON.stringify(data), (response) => {
            console.log(response);
          });
        }
      }

      function add_option(parent, content) {
        let li = document.createElement("li");

        li.innerHTML = content;
        parent.appendChild(li);

        return li;
      }

      function add_list(parent, content) {
        let option = document.createElement("option");

        option.innerHTML = content;
        parent.appendChild(option);

        return option;
      }
    </script>
  </body>
</html>
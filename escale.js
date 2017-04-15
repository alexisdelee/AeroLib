var state = 1;
var date = null;
var data = {
  date: 0,
  prestation: null,
  state: null,
  acoustic: null,
  model: null,
  service: null,
  duration: 1
};

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

        request.post("getInformation.php", "get=forfait&type=Atterrissage&date=" + data.prestation, (response) => {
          console.log(response);
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
        dialog("Date non comprise entre 30h et 1 an.", true);
      }
    } else {
      dialog("Date invalide", true);
    }
  } else if(state & 4) { // on analyse le modèle et le groupe acoustique
    let request = new Request();
    request.post("getInformation.php", "get=model", (response) => {
      if(response !== "undefined") {
        for(let model of response.split(":")) {
          add_list(model_container, model);
        }
      } else {
        dialog("Erreur.", true);
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
      } else {
        dialog("Erreur.", true);
      }
    });
  } else if(state & 16) { // envoie des données pour inscription
    let request = new Request();
    request.post("getInformation.php", "type=landing&domain=register&data=" + JSON.stringify(data), (response) => {
      console.log(response);
      if(response === "undefined") {
        dialog("Erreur.", true);
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
    dialog("Cette prestation a été ajoutée à votre panier.");
  } else if(state & 64) { // finalisation de la facture
    dialog("Vous avez bien été enregistré(e) pour le " + moment(date.UTCToLocale("Europe/London")).format("DD/MM/YYYY à HH:mm") + ".<br>Merci de bien vouloir confirmer votre venu minimum 24h avant votre atterrissage.");
  } else if(state & 128) {
    dialog("Impossible d'attérir à cette heure-là, l'emplacement est déjà réservé.", true);
  } else if(state & 256) { // réactulisation du service
    let manager = new MomentUtils();

    let request = new Request();
    request.post("getInformation.php", "type=landing&domain=update&data=" + JSON.stringify(data), (response) => {
      switch(response) {
        case "undefined": // non reconnu par le système
          dialog("Aucune demande pour le " + moment(option, "DD/MM/YYYY HH:mm").format("DD/MM/YYYY à HH:mm") + " venant de vous n'a été enregistrée.", true);
          break;
        case "too_early": // plus de 48h en avance
          dialog("Vous avez confirmé trop tôt votre présence pour le " + moment(option, "DD/MM/YYYY HH:mm").format("DD/MM/YYYY à HH:mm") + ".<br>Veuillez réessayer maximum 48h avant votre atterrissage.", true);
          break;
        case "ok": // pile dans le temps
          dialog("Votre demande d'atterrissage pour le " + moment(option, "DD/MM/YYYY HH:mm").format("DD/MM/YYYY à HH:mm") + " a bien été vérifiée et acceptée.");
          break;
        case "too_late": // inférieur à 24h avant le mouvement
          dialog("Vous n'avez pas pu confirmer 24h en avance votre atterrissage.<br>Votre demande a été annulée.", true);
          break;
      }
    });
  }
}

function reservoir(option) {
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

        request.post("getInformation.php", "get=forfait&type=Avitaillement&date=" + data.prestation, (response) => {
          console.log(response);
          if(response === "unavailable") {
            state = 128;
          } else {
            state = 4;
          }

          return reservoir();
        });
      } else {
        dialog("Date non comprise entre 30h et 1 an.", true);
      }
    } else {
      dialog("Date invalide", true);
    }
  } else if(state & 4) {
    // do something
  } else if(state & 128) {
    dialog("L'équipe de nettoyage est déjà reservée pour cette heure-là.", true);
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

function dialog(msg, error = false) {
  /* let li = document.createElement("li");
  li.innerHTML = msg;

  if(error) li.style.style = "red";
  popup.manager.open(li); */
  let box = "<span" + (error ? " style=\"color: #A61835\"" : "") + ">" + msg + "</span>";
  popup.manager.open(box);
}
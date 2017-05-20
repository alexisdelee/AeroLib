let prestations = document.querySelectorAll(".tabs");
for(let prestation of prestations) {
  let link = prestation.querySelector("a");
  link.addEventListener("click", (e) => {
    e.preventDefault();

    let old = document.querySelector(".open");
    if(old !== null) {
      old.classList.remove("open");
    }

    e.target.parentNode.parentNode.classList.add("open");
  });
}

let send = document.querySelectorAll(".send");
if(send != null) {
  for(let button of send) {
    button.addEventListener("click", (e) => {
      let data = "";

      if(e.target.dataset.prestation == "simple_services") {
        let container = document.querySelector(".simple_services").parentNode;

        data = "&title=" + encodeURIComponent(container.querySelector("select").selectedOptions[0].value);
        data += "&duration=" + (isNaN(parseInt(container.querySelector("input[type=\"text\"]").value)) ? 0 : container.querySelector("input[type=\"text\"]").value);
      } else if(e.target.dataset.prestation == "extra_service") {
        let container = document.querySelector(".extra_service").parentNode;

        data = "&title=" + encodeURIComponent(container.querySelector("select").selectedOptions[0].value);
        data += "&member=" + (container.querySelector("#club").checked ? "true" : "false");
        data += "&revue=" + (container.querySelector("#revue").checked ? "true" : "false");
      }

      window.location.href = e.target.dataset.href + data;
    });
  }
}

function getQueryVariable(_param) { // pour récupérer les paramètres passés dans l'url
  let url = window.location.href;

  _param = _param.replace(/[\[\]]/g, "\\$&");
  let regex = new RegExp("[?&]" + _param + "(=([^&#]*)|&|#|$)"),
      results = regex.exec(url);

  if(!results) return null;
  else if(!results[2]) return "";
  else return decodeURIComponent(results[2].replace(/\+/g, " "));
}

let valid = document.querySelector("#accept");
if(valid !== null) {
  valid.addEventListener("click", () => {
    let action = document.querySelector("#date");
    action = (action == null || action.value == "" ? "01/01/1970 01:00" : action.value);

    let pattern = new RegExp("([0-9]{2}/){2}[0-9]{4} [0-9]{2}:[0-9]{2}", "g");
    if(!pattern.test(action)) {
      popup.manager.open("<span style=\"color: #A61835\">Veuillez rentrer une date valide</span>");
      return;
    }

    let manager = new MomentUtils();
    let data = decodeURIComponent(window.location.search.substring(1));
    let prestation_date = moment(action, "DD/MM/YYYY HH:mm")._d;
    data += "&action=" + manager.timestamp(prestation_date);
    data += "&age=" + fill[2];
    data += "&name=" + fill[1];

    if(prestation_date == "Invalid Date") {
      popup.manager.open("<span style=\"color: #A61835\">Veuillez rentrer une date valide</span>");
      return;
    }

    let now = new Date();

    if(new Date(now.getFullYear(), 3, 15, 0, 0, 0) > prestation_date || new Date(now.getFullYear(), 9, 15, 15, 59) < prestation_date) { // non compris entre le 15 avril et le 15 octobre
      if(prestation_date.getDay() != 0 && prestation_date.getDay() != 6) {
        if(!moment(moment(prestation_date).format("DD-MM-YYYY"), "DD-MM-YYYY").isFerie()) {
          popup.manager.open("<span style=\"color: #A61835\">Veuillez nous excuser, l'aéroclub est fermé à cette date</span>");
          return;
        }
      }
    }

    if(getQueryVariable("prestation") == "simple_service" || getQueryVariable("prestation") == "extra_service") {
      sendPrestation(data, (response) => {
        response = JSON.parse(response);

        if(response.status == 200) {
          window.location.href = "aeroclub.php";
        } else {
          popup.manager.open("<span style=\"color: #A61835\">" + decodeURIComponent(response.message) + "</span>");
        }
      });
    }
  });
}

function sendPrestation(data, callback) {
  let request = new Request();
  request.post("prestations.php", data, (response) => {
    if(callback != undefined) callback(response);
  });
}